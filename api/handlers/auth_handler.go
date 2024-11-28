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
	var user models.User
	if err := c.ShouldBindJSON(&user); err != nil {
		logger.LogError("Register", err, fmt.Sprintf("Invalid request data: %+v", c.Request.Body))
		c.JSON(http.StatusBadRequest, gin.H{"error": "Пожалуйста, заполните все поля"})
		return
	}

	// Проверяем все обязательные поля
	if user.Name == "" || user.Email == "" || user.Password == "" || user.Phone == "" || user.Category == "" {
		logger.LogError("Register", fmt.Errorf("missing required fields"), 
			fmt.Sprintf("User data: %+v", user))
		c.JSON(http.StatusBadRequest, gin.H{"error": "Все поля обязательны для заполнения"})
		return
	}

	// Проверка существующего пользователя
	var existingUser models.User
	if err := database.DB.Where("email = ?", user.Email).First(&existingUser).Error; err == nil {
		if !existingUser.IsVerified {
			// Удаляем старую неверифицированную запись
			if err := database.DB.Unscoped().Delete(&existingUser).Error; err != nil {
				logger.LogError("Register", err, fmt.Sprintf("Failed to delete unverified user: %s", user.Email))
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
	user.OTPCode = otp
	user.IsVerified = false

	// Хеширование пароля
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(user.Password), bcrypt.DefaultCost)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обработке пароля"})
		return
	}
	user.Password = string(hashedPassword)

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
			"id": user.ID,
			"email": user.Email,
			"name": user.Name,
			"category": user.Category,
			"is_verified": user.IsVerified,
			"phone": user.Phone,
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
		"name":        user.Name,
		"email":       user.Email,
		"phone":       user.Phone,
		"category":    user.Category,
		"created_at":  user.CreatedAt,
		"is_verified": user.IsVerified,
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
		"name":        user.Name,
		"email":       user.Email,
		"phone":       user.Phone,
		"category":    user.Category,
		"created_at":  user.CreatedAt,
		"is_verified": user.IsVerified,
	}

	c.JSON(http.StatusOK, userResponse)
} 