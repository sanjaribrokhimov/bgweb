package handlers

import (
	"fmt"
	"log"
	"math/rand"
	"net/http"
	"strconv"

	"bloger_agencyBackend/database"
	"bloger_agencyBackend/models"
	"bloger_agencyBackend/utils"

	"github.com/gin-gonic/gin"
	"golang.org/x/crypto/bcrypt"
	"gorm.io/gorm"
)

var logger = utils.NewLogger()

func Register(c *gin.Context) {
	var input struct {
		Identifier string `json:"identifier" binding:"required"`
		Password   string `json:"password" binding:"required"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		logger.LogError("Register", err, fmt.Sprintf("Invalid request data: %+v", c.Request.Body))
		c.JSON(http.StatusBadRequest, gin.H{"error": "Пожалуйста, укажите identifier и пароль"})
		return
	}

	// Определяем тип идентификатора (email или chat_id)
	isEmail := false
	for _, char := range input.Identifier {
		if char == '@' {
			isEmail = true
			break
		}
	}

	var email, chatID string
	if isEmail {
		email = input.Identifier
	} else {
		chatID = input.Identifier
	}

	// Проверка существующего пользователя
	var existingUserByEmail models.User
	var existingUserByChatID models.User

	// Проверяем существование пользователя по email
	if email != "" {
		if err := database.DB.Where("email = ? AND email != ''", email).First(&existingUserByEmail).Error; err == nil {
			if existingUserByEmail.IsVerified {
				c.JSON(http.StatusBadRequest, gin.H{"error": "Пользователь с таким email уже зарегистрирован"})
				return
			} else {
				// Удаляем старую неверифицированную запись
				if err := database.DB.Unscoped().Delete(&existingUserByEmail).Error; err != nil {
					logger.LogError("Register", err, "Failed to delete unverified user")
					c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обработке регистрации"})
					return
				}
			}
		}
	}

	// Проверяем существование пользователя по chat_id
	if chatID != "" {
		if err := database.DB.Where("tg_chat_id = ?", chatID).First(&existingUserByChatID).Error; err == nil {
			if existingUserByChatID.IsVerified {
				c.JSON(http.StatusBadRequest, gin.H{"error": "Пользователь с таким chat ID уже зарегистрирован"})
				return
			} else {
				// Удаляем старую неверифицированную запись
				if err := database.DB.Unscoped().Delete(&existingUserByChatID).Error; err != nil {
					logger.LogError("Register", err, "Failed to delete unverified user")
					c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обработке регистрации"})
					return
				}
			}
		}
	}

	// Дополнительная проверка на пустые значения
	if email == "" && chatID == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Необходимо указать email или chat ID"})
		return
	}

	// Генерация OTP
	otp := strconv.Itoa(1000 + rand.Intn(9000))

	// Хеширование пароля
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(input.Password), bcrypt.DefaultCost)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обработке пароля"})
		return
	}

	// Создаем пользователя
	user := models.User{
		Name:       "empty",
		Email:      email,
		Password:   string(hashedPassword),
		Phone:      "empty",
		Category:   "empty",
		Direction:  "empty",
		Telegram:   "empty",
		Instagram:  "empty",
		OTPCode:    otp,
		IsVerified: false,
		TgChatID:   chatID,
		TgUserID:   "empty",
	}

	// Сохранение пользователя
	if err := database.DB.Create(&user).Error; err != nil {
		logger.LogError("Register", err, "Failed to create user")
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при создании пользователя"})
		return
	}

	var otpError error

	// Отправляем OTP в зависимости от типа идентификатора
	if isEmail {
		if err := utils.SendOTP(email, otp); err != nil {
			logger.LogError("Register", err, "Failed to send OTP via email")
			otpError = err
		}
	} else {
		if err := utils.SendTelegramOTP(chatID, otp); err != nil {
			logger.LogError("Register", err, "Failed to send OTP via Telegram")
			otpError = err
		}
	}

	if otpError != nil {
		// Удаляем созданного пользователя
		database.DB.Unscoped().Delete(&user)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при отправке кода подтверждения: " + otpError.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Регистрация успешна. Проверьте сообщение с кодом подтверждения."})
}

func VerifyOTP(c *gin.Context) {
	var input struct {
		Identifier string `json:"identifier"`
		OTP        string `json:"otp"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Неверный формат данных"})
		return
	}

	// Определяем тип идентификатора (email или chat_id)
	isEmail := false
	for _, char := range input.Identifier {
		if char == '@' {
			isEmail = true
			break
		}
	}

	var user models.User
	query := database.DB
	if isEmail {
		query = query.Where("email = ?", input.Identifier)
	} else {
		query = query.Where("tg_chat_id = ?", input.Identifier)
	}

	if err := query.First(&user).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Пользователь не найден"})
		return
	}

	if user.OTPCode != input.OTP {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Неверный код подтверждения"})
		return
	}

	// Подтверждаем пользователя
	user.IsVerified = true
	user.OTPCode = ""
	if err := database.DB.Save(&user).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при подтверждении аккаунта"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message": "Аккаунт успешно подтвержден",
		"user": gin.H{
			"id":          user.ID,
			"email":       user.Email,
			"name":        user.Name,
			"category":    user.Category,
			"direction":   user.Direction,
			"telegram":    user.Telegram,
			"instagram":   user.Instagram,
			"is_verified": user.IsVerified,
			"phone":       user.Phone,
			"tg_chat_id":  user.TgChatID,
			"tg_user_id":  user.TgUserID,
		},
	})
}

func Login(c *gin.Context) {
	var input struct {
		Identifier string `json:"identifier"` // Может быть email или chat_id
		Password   string `json:"password"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		logger.LogError("Login", err, "Invalid login data format")
		c.JSON(http.StatusBadRequest, gin.H{"error": "Введите идентификатор (email или chat ID) и пароль"})
		return
	}

	// Определяем тип идентификатора (email или chat_id)
	isEmail := false
	for _, char := range input.Identifier {
		if char == '@' {
			isEmail = true
			break
		}
	}

	var user models.User
	if isEmail {
		// Поиск по email
		if err := database.DB.Where("email = ?", input.Identifier).First(&user).Error; err != nil {
			logger.LogError("Login", err, fmt.Sprintf("User not found: %s", input.Identifier))
			c.JSON(http.StatusUnauthorized, gin.H{"error": "Неверный email или пароль"})
			return
		}
	} else {
		// Поиск по chat_id
		if err := database.DB.Where("tg_chat_id = ?", input.Identifier).First(&user).Error; err != nil {
			logger.LogError("Login", err, fmt.Sprintf("User not found with chat_id: %s", input.Identifier))
			c.JSON(http.StatusUnauthorized, gin.H{"error": "Неверный chat ID или пароль"})
			return
		}
	}

	if !user.IsVerified {
		c.JSON(http.StatusUnauthorized, gin.H{
			"error":      "Аккаунт не подтвержден",
			"isVerified": false,
		})
		return
	}

	if err := bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(input.Password)); err != nil {
		c.JSON(http.StatusUnauthorized, gin.H{"error": "Неверный идентификатор или пароль"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message": "Вход выполнен успешно",
		"user": gin.H{
			"id":          user.ID,
			"email":       user.Email,
			"name":        user.Name,
			"category":    user.Category,
			"direction":   user.Direction,
			"telegram":    user.Telegram,
			"instagram":   user.Instagram,
			"is_verified": user.IsVerified,
			"phone":       user.Phone,
			"tg_chat_id":  user.TgChatID,
			"tg_user_id":  user.TgUserID,
		},
	})
}

func ResendOTP(c *gin.Context) {
	var input struct {
		Email string `json:"email"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Email обязателен"})
		return
	}

	var user models.User
	if err := database.DB.Where("email = ?", input.Email).First(&user).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Пользователь не найден"})
		return
	}

	// Генерируем новый OTP
	newOTP := strconv.Itoa(1000 + rand.Intn(9000))
	user.OTPCode = newOTP

	if err := database.DB.Save(&user).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обновлении кода"})
		return
	}

	if err := utils.SendOTP(user.Email, newOTP); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при отправке нового кода"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Новый код отправлен"})
}

func GetUserByEmail(c *gin.Context) {
	identifier := c.Query("identifier")
	log.Printf("Получен запрос для идентификатора: %s", identifier)

	if identifier == "" {
		log.Printf("Идентификатор не указан в запросе")
		c.JSON(http.StatusBadRequest, gin.H{"error": "Identifier is required"})
		return
	}

	// Определяем тип идентификатора (email или chat_id)
	isEmail := false
	for _, char := range identifier {
		if char == '@' {
			isEmail = true
			break
		}
	}

	var user models.User
	var result *gorm.DB

	if isEmail {
		result = database.DB.Where("email = ?", identifier).First(&user)
	} else {
		result = database.DB.Where("tg_chat_id = ?", identifier).First(&user)
	}

	if result.Error != nil {
		log.Printf("Ошибка при поиске пользователя: %v", result.Error)
		c.JSON(http.StatusNotFound, gin.H{"error": "User not found"})
		return
	}

	log.Printf("Пользователь найден: %+v", user)

	userResponse := gin.H{
		"id":          user.ID,
		"email":       user.Email,
		"name":        user.Name,
		"category":    user.Category,
		"direction":   user.Direction,
		"telegram":    user.Telegram,
		"instagram":   user.Instagram,
		"is_verified": user.IsVerified,
		"phone":       user.Phone,
		"tg_chat_id":  user.TgChatID,
		"tg_user_id":  user.TgUserID,
	}

	c.JSON(http.StatusOK, userResponse)
}

func GetUserByID(c *gin.Context) {
	userID := c.Param("id")

	var user models.User
	if err := database.DB.First(&user, userID).Error; err != nil {
		logger.LogError("GetUserByID", err, fmt.Sprintf("User not found with ID: %s", userID))
		c.JSON(http.StatusNotFound, gin.H{"error": "Пользователь не найден"})
		return
	}

	// Формируем ответ без конфиденциальных данных
	userResponse := gin.H{
		"id":          user.ID,
		"email":       user.Email,
		"name":        user.Name,
		"category":    user.Category,
		"direction":   user.Direction,
		"telegram":    user.Telegram,
		"instagram":   user.Instagram,
		"is_verified": user.IsVerified,
		"phone":       user.Phone,
		"tg_chat_id":  user.TgChatID,
		"tg_user_id":  user.TgUserID,
	}

	c.JSON(http.StatusOK, userResponse)
}

// ForgotPassword обрабатывает запрос на восстановление пароля
func ForgotPassword(c *gin.Context) {
	var input struct {
		Email string `json:"email" binding:"required,email"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		logger.LogError("ForgotPassword", err, "Invalid input data")
		c.JSON(http.StatusBadRequest, gin.H{
			"error": "Пожалуйста, введите корректный email",
		})
		return
	}

	// Логируем входящий запрос
	log.Printf("Получен запрос на восстановление пароля для email: %s", input.Email)

	// Проверяем существование пользователя
	var user models.User
	if err := database.DB.Where("email = ?", input.Email).First(&user).Error; err != nil {
		log.Printf("Пользователь не найден: %v", err)
		c.JSON(http.StatusNotFound, gin.H{
			"error": "Пользователь с таким email не найден",
		})
		return
	}

	// Генерируем новый OTP код
	otp := strconv.Itoa(1000 + rand.Intn(9000))
	log.Printf("Сгенерирован OTP код для пользователя %s", input.Email)

	// Сохраняем OTP в базе данных
	user.OTPCode = otp
	if err := database.DB.Save(&user).Error; err != nil {
		logger.LogError("ForgotPassword", err, "Failed to save OTP")
		log.Printf("Ошибка при сохранении OTP: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{
			"error": "Ошибка сервера при обработке запроса",
		})
		return
	}

	// Отправляем OTP на email
	if err := utils.SendOTP(user.Email, otp); err != nil {
		logger.LogError("ForgotPassword", err, "Failed to send OTP")
		log.Printf("Ошибка при отправке OTP: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{
			"error": "Ошибка при отправке кода подтверждения",
		})
		return
	}

	log.Printf("OTP код успешно отправлен на email: %s", input.Email)
	c.JSON(http.StatusOK, gin.H{
		"message": "Код подтверждения отправлен на ваш email",
		"email":   input.Email,
	})
}

// ResetPassword обрабатывает изменение пароля
func ResetPassword(c *gin.Context) {
	var input struct {
		Email    string `json:"email" binding:"required,email"`
		Password string `json:"password" binding:"required,min=6"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Некорректные данные"})
		return
	}

	// Находим пользователя
	var user models.User
	if err := database.DB.Where("email = ?", input.Email).First(&user).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Пользователь не найден"})
		return
	}

	// Хешируем новый пароль
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(input.Password), bcrypt.DefaultCost)
	if err != nil {
		logger.LogError("ResetPassword", err, "Failed to hash password")
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обработке пароля"})
		return
	}

	// Обновляем пароль
	user.Password = string(hashedPassword)
	if err := database.DB.Save(&user).Error; err != nil {
		logger.LogError("ResetPassword", err, "Failed to save new password")
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при сохранении пароля"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Пароль успешно изменен"})
}

// GetUserProfile возвращает профиль пользователя
func GetUserProfile(c *gin.Context) {
	email := c.Query("email")
	if email == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Email обязателен"})
		return
	}

	var user models.User
	if err := database.DB.Where("email = ?", email).First(&user).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Пользователь не найден"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"id":          user.ID,
		"name":        user.Name,
		"email":       user.Email,
		"phone":       user.Phone,
		"category":    user.Category,
		"direction":   user.Direction,
		"telegram":    user.Telegram,
		"instagram":   user.Instagram,
		"is_verified": user.IsVerified,
		"tg_chat_id":  user.TgChatID,
		"tg_user_id":  user.TgUserID,
	})
}

// UpdateUserProfile обновляет профиль пользователя
func UpdateUserProfile(c *gin.Context) {
	var input struct {
		Email     string `json:"email" binding:"required,email"`
		Name      string `json:"name" binding:"required"`
		Phone     string `json:"phone" binding:"required"`
		Category  string `json:"category" binding:"required"`
		Direction string `json:"direction" binding:"required"`
		Telegram  string `json:"telegram" binding:"required"`
		Instagram string `json:"instagram" binding:"required"`
		TgChatID  string `json:"tg_chat_id" binding:"required"`
		TgUserID  string `json:"tg_user_id" binding:"required"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Некорректные данные"})
		return
	}

	var user models.User
	if err := database.DB.Where("email = ?", input.Email).First(&user).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Пользователь не найден"})
		return
	}

	// Обновляем поля
	user.Name = input.Name
	user.Phone = input.Phone
	user.Category = input.Category
	user.Direction = input.Direction
	user.Telegram = input.Telegram

	if err := database.DB.Save(&user).Error; err != nil {
		logger.LogError("UpdateUserProfile", err, "Failed to update user profile")
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обновлении профиля"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message": "Профиль успешно обновлен",
		"user": gin.H{
			"id":          user.ID,
			"name":        user.Name,
			"email":       user.Email,
			"phone":       user.Phone,
			"category":    user.Category,
			"direction":   user.Direction,
			"telegram":    user.Telegram,
			"instagram":   user.Instagram,
			"is_verified": user.IsVerified,
			"tg_chat_id":  user.TgChatID,
			"tg_user_id":  user.TgUserID,
		},
	})
}

// UpdateProfile обновляет данные профиля пользователя
func UpdateProfile(c *gin.Context) {
	var input struct {
		Email     string `json:"email"`
		Name      string `json:"name,omitempty"`
		Phone     string `json:"phone,omitempty"`
		Telegram  string `json:"telegram,omitempty"`
		Instagram string `json:"instagram,omitempty"`
		Category  string `json:"category,omitempty"`
		Direction string `json:"direction,omitempty"`
		Password  string `json:"password,omitempty"`
		TgChatID  string `json:"tg_chat_id" binding:"required"`
		TgUserID  string `json:"tg_user_id" binding:"required"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Некорректные данные"})
		return
	}

	var user models.User
	if err := database.DB.Where("email = ?", input.Email).First(&user).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Пользователь не найден"})
		return
	}

	// Обновляем только те поля, которые были переданы
	if input.Name != "" {
		user.Name = input.Name
	}
	if input.Phone != "" {
		user.Phone = input.Phone
	}
	if input.Telegram != "" {
		user.Telegram = input.Telegram
	}
	if input.Category != "" {
		user.Category = input.Category
	}
	if input.Direction != "" {
		user.Direction = input.Direction
	}

	// Если указан новый пароль, обновляем его
	if input.Password != "" {
		hashedPassword, err := bcrypt.GenerateFromPassword([]byte(input.Password), bcrypt.DefaultCost)
		if err != nil {
			c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обработке пароля"})
			return
		}
		user.Password = string(hashedPassword)
	}

	// Сохраняем обновленные данные
	if err := database.DB.Save(&user).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обновлении профиля"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message": "Профиль успешно обновлен",
		"user": gin.H{
			"id":          user.ID,
			"name":        user.Name,
			"email":       user.Email,
			"phone":       user.Phone,
			"telegram":    user.Telegram,
			"instagram":   user.Instagram,
			"category":    user.Category,
			"direction":   user.Direction,
			"is_verified": user.IsVerified,
			"tg_chat_id":  user.TgChatID,
			"tg_user_id":  user.TgUserID,
		},
	})
}

// AdminUpdateUser обновляет профиль пользователя через админку
func AdminUpdateUser(c *gin.Context) {
	var input struct {
		Email     string `json:"email" binding:"required"`
		Name      string `json:"name" binding:"required"`
		Phone     string `json:"phone" binding:"required"`
		Telegram  string `json:"telegram" binding:"required"`
		Instagram string `json:"instagram" binding:"required"`
		Category  string `json:"category" binding:"required"`
		Direction string `json:"direction" binding:"required"`
	}

	// Логируем входящие данные
	body, _ := c.GetRawData()
	log.Printf("Received data: %s", string(body))

	if err := c.ShouldBindJSON(&input); err != nil {
		log.Printf("Error binding JSON: %v", err)
		c.JSON(http.StatusBadRequest, gin.H{
			"error":   "Некорректные данные",
			"details": err.Error(),
		})
		return
	}

	var user models.User
	if err := database.DB.Where("email = ?", input.Email).First(&user).Error; err != nil {
		log.Printf("Error finding user: %v", err)
		c.JSON(http.StatusNotFound, gin.H{"error": "Пользователь не найден"})
		return
	}

	// Обновляем поля
	user.Name = input.Name
	user.Phone = input.Phone
	user.Telegram = input.Telegram
	user.Instagram = input.Instagram
	user.Category = input.Category
	user.Direction = input.Direction

	if err := database.DB.Save(&user).Error; err != nil {
		log.Printf("Error saving user: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обновлении профиля"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"success": true,
		"message": "Профиль успешно обновлен",
		"user": gin.H{
			"id":          user.ID,
			"name":        user.Name,
			"email":       user.Email,
			"phone":       user.Phone,
			"category":    user.Category,
			"direction":   user.Direction,
			"telegram":    user.Telegram,
			"instagram":   user.Instagram,
			"is_verified": user.IsVerified,
		},
	})
}

// CheckUserFields проверяет, заполнены ли все поля пользователя
func CheckUserFields(c *gin.Context) {
	userID := c.Param("id")
	log.Printf("Получен запрос на проверку полей для ID: %s", userID)

	var user models.User
	result := database.DB.First(&user, userID)
	if result.Error != nil {
		log.Printf("Ошибка при поиске пользователя: %v", result.Error)
		log.Printf("Искомый ID: %s", userID)

		logger.LogError("CheckUserFields", result.Error, fmt.Sprintf("User not found with ID: %s", userID))
		c.JSON(http.StatusNotFound, gin.H{
			"error":   "Пользователь не найден",
			"details": fmt.Sprintf("Не удалось найти пользователя с ID: %s", userID),
		})
		return
	}

	log.Printf("Пользователь найден: %+v", user)

	// Проверяем все поля на "empty"
	isComplete := user.Name != "empty" &&
		user.Phone != "empty" &&
		user.Category != "empty" &&
		user.Direction != "empty" &&
		user.Telegram != "empty" &&
		user.Instagram != "empty"

	c.JSON(http.StatusOK, gin.H{
		"is_complete": isComplete,
		"user": gin.H{
			"id":          user.ID,
			"email":       user.Email,
			"name":        user.Name,
			"category":    user.Category,
			"direction":   user.Direction,
			"telegram":    user.Telegram,
			"instagram":   user.Instagram,
			"is_verified": user.IsVerified,
			"phone":       user.Phone,
			"tg_chat_id":  user.TgChatID,
			"tg_user_id":  user.TgUserID,
		},
	})
}

// CompleteRegistration обновляет данные пользователя после первичной регистрации
func CompleteRegistration(c *gin.Context) {
	userID := c.Param("id")

	var input struct {
		Name      string `json:"name" binding:"required"`
		Phone     string `json:"phone" binding:"required"`
		Category  string `json:"category" binding:"required"`
		Direction string `json:"direction" binding:"required"`
		Telegram  string `json:"telegram" binding:"required"`
		Instagram string `json:"instagram" binding:"required"`
		Email     string `json:"email" binding:"required,email"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		logger.LogError("CompleteRegistration", err, "Invalid input data")
		c.JSON(http.StatusBadRequest, gin.H{
			"error": "Все поля обязательны для заполнения",
		})
		return
	}

	// Проверяем, не занят ли уже этот email другим пользователем
	var existingUser models.User
	if err := database.DB.Where("email = ? AND id != ?", input.Email, userID).First(&existingUser).Error; err == nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"error": "Этот email уже используется другим пользователем",
		})
		return
	}

	var user models.User
	if err := database.DB.First(&user, userID).Error; err != nil {
		logger.LogError("CompleteRegistration", err, fmt.Sprintf("User not found with ID: %s", userID))
		c.JSON(http.StatusNotFound, gin.H{
			"error": "Пользователь не найден",
		})
		return
	}

	// Обновляем данные пользователя
	user.Name = input.Name
	user.Phone = input.Phone
	user.Category = input.Category
	user.Direction = input.Direction
	user.Telegram = input.Telegram
	user.Instagram = input.Instagram

	// Если email изменился, отправляем OTP для подтверждения
	if input.Email != user.Email {
		// Генерация OTP
		otp := strconv.Itoa(1000 + rand.Intn(9000))
		user.OTPCode = otp
		user.IsVerified = false
		user.Email = input.Email

		// Отправляем OTP на новый email
		if err := utils.SendOTP(input.Email, otp); err != nil {
			logger.LogError("CompleteRegistration", err, "Failed to send OTP")
			c.JSON(http.StatusInternalServerError, gin.H{
				"error": "Ошибка при отправке кода подтверждения",
			})
			return
		}

		if err := database.DB.Save(&user).Error; err != nil {
			logger.LogError("CompleteRegistration", err, "Failed to update user")
			c.JSON(http.StatusInternalServerError, gin.H{
				"error": "Ошибка при обновлении данных",
			})
			return
		}

		c.JSON(http.StatusOK, gin.H{
			"message":             "Данные обновлены. Для подтверждения email введите код, отправленный на почту",
			"requireVerification": true,
			"user": gin.H{
				"id":          user.ID,
				"email":       user.Email,
				"name":        user.Name,
				"category":    user.Category,
				"direction":   user.Direction,
				"telegram":    user.Telegram,
				"instagram":   user.Instagram,
				"is_verified": user.IsVerified,
				"phone":       user.Phone,
				"tg_chat_id":  user.TgChatID,
				"tg_user_id":  user.TgUserID,
			},
		})
		return
	}

	// Если email не изменился, просто сохраняем обновленные данные
	if err := database.DB.Save(&user).Error; err != nil {
		logger.LogError("CompleteRegistration", err, "Failed to update user")
		c.JSON(http.StatusInternalServerError, gin.H{
			"error": "Ошибка при обновлении данных",
		})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message": "Регистрация успешно завершена",
		"user": gin.H{
			"id":          user.ID,
			"email":       user.Email,
			"name":        user.Name,
			"category":    user.Category,
			"direction":   user.Direction,
			"telegram":    user.Telegram,
			"instagram":   user.Instagram,
			"is_verified": user.IsVerified,
			"phone":       user.Phone,
			"tg_chat_id":  user.TgChatID,
			"tg_user_id":  user.TgUserID,
		},
	})
}
