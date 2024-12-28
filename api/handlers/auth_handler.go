package handlers

import (
	"math/rand"
	"net/http"
	"strconv"
	"log"
	"fmt"
	

	"github.com/gin-gonic/gin"
	"golang.org/x/crypto/bcrypt"
	"bloger_agencyBackend/database"
	"bloger_agencyBackend/models"
	"bloger_agencyBackend/utils"
)

var logger = utils.NewLogger()

func Register(c *gin.Context) {
	var input struct {
		Name      string `json:"name" binding:"required"`
		Email     string `json:"email" binding:"required"`
		Password  string `json:"password" binding:"required"`
		Phone     string `json:"phone" binding:"required"`
		Category  string `json:"category" binding:"required"`
		Direction string `json:"direction" binding:"required"`
		Telegram  string `json:"telegram" binding:"required"`
		Instagram string `json:"instagram" binding:"required"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		logger.LogError("Register", err, fmt.Sprintf("Invalid request data: %+v", c.Request.Body))
		c.JSON(http.StatusBadRequest, gin.H{"error": "Пожалуйста, заполните все поля"})
		return
	}

	// Проверяем все обязательные поля
	if input.Name == "" || input.Email == "" || input.Password == "" || 
	   input.Phone == "" || input.Category == "" || input.Direction == "" || 
	   input.Telegram == "" {
		logger.LogError("Register", fmt.Errorf("missing required fields"), 
			fmt.Sprintf("User data: %+v", input))
		c.JSON(http.StatusBadRequest, gin.H{"error": "Все поля обязательны для заполнения"})
		return
	}

	// Проверка существующего пользователя
	var existingUser models.User
	if err := database.DB.Where("email = ?", input.Email).First(&existingUser).Error; err == nil {
		if !existingUser.IsVerified {
			// Удаляем старую неверифицированную запись
			if err := database.DB.Unscoped().Delete(&existingUser).Error; err != nil {
				logger.LogError("Register", err, fmt.Sprintf("Failed to delete unverified user: %s", input.Email))
				c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обработке регистрации"})
				return
			}
		} else {
			c.JSON(http.StatusBadRequest, gin.H{"error": "Этот email уже зарегистрирован"})
			return
		}
	}

	// Генерация OTP
	otp := strconv.Itoa(1000 + rand.Intn(9000))

	// Хешир��вание пароля
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(input.Password), bcrypt.DefaultCost)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обработке пароля"})
		return
	}

	// Создаем пользователя
	user := models.User{
		Name:       input.Name,
		Email:      input.Email,
		Password:   string(hashedPassword),
		Phone:      input.Phone,
		Category:   input.Category,
		Direction:  input.Direction,
		Telegram:   input.Telegram,
		Instagram:  input.Instagram,
		OTPCode:    otp,
		IsVerified: false,
	}

	// Сохранение пользователя
	if err := database.DB.Create(&user).Error; err != nil {
		logger.LogError("Register", err, fmt.Sprintf("Failed to create user: %s", user.Email))
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при создании пользователя"})
		return
	}

	// Отправка OTP
	if err := utils.SendOTP(user.Email, otp); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при отправке кода подтверждения"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Регистрация успешна. Проверьте email для подтверждения."})
}

func VerifyOTP(c *gin.Context) {
	var input struct {
		Email string `json:"email"`
		OTP   string `json:"otp"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Неверный формат данных"})
		return
	}

	var user models.User
	if err := database.DB.Where("email = ?", input.Email).First(&user).Error; err != nil {
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

	c.JSON(http.StatusOK, gin.H{"message": "Email успешно подтвержден"})
}

func Login(c *gin.Context) {
	var input struct {
		Email    string `json:"email"`
		Password string `json:"password"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		logger.LogError("Login", err, "Invalid login data format")
		c.JSON(http.StatusBadRequest, gin.H{"error": "Введите email и пароль"})
		return
	}

	var user models.User
	if err := database.DB.Where("email = ?", input.Email).First(&user).Error; err != nil {
		logger.LogError("Login", err, fmt.Sprintf("User not found: %s", input.Email))
		c.JSON(http.StatusUnauthorized, gin.H{"error": "Неверный email или пароль"})
		return
	}

	if !user.IsVerified {
		c.JSON(http.StatusUnauthorized, gin.H{
			"error": "Email не подтвержден",
			"isVerified": false,
		})
		return
	}

	if err := bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(input.Password)); err != nil {
		c.JSON(http.StatusUnauthorized, gin.H{"error": "Неверный email или пароль"})
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
	email := c.Query("email")
	log.Printf("Получен запрос для email: %s", email)

	if email == "" {
		log.Printf("Email не указан в запросе")
		c.JSON(http.StatusBadRequest, gin.H{"error": "Email is required"})
		return
	}

	var user models.User
	result := database.DB.Where("email = ?", email).First(&user)
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
		"email": input.Email,
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
		},
	})
}

// UpdateProfile обновляет данные профиля пользователя
func UpdateProfile(c *gin.Context) {
	var input struct {
		Email       string `json:"email"`
		Name        string `json:"name,omitempty"`
		Phone       string `json:"phone,omitempty"`
		Telegram    string `json:"telegram,omitempty"`
		Instagram   string `json:"instagram,omitempty"`
		Category    string `json:"category,omitempty"`
		Direction   string `json:"direction,omitempty"`
		Password    string `json:"password,omitempty"`
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
		},
	})
} 