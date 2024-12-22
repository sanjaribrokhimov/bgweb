package handlers

import (
	"bloger_agencyBackend/database"
	"bloger_agencyBackend/models"
	"bloger_agencyBackend/utils"
	"fmt"
	"net/http"

	"github.com/gin-gonic/gin"
	"golang.org/x/crypto/bcrypt"
)

// AdminLogin обрабатывает вход администратора
func AdminLogin(c *gin.Context) {
	var input struct {
		Username string `json:"username" binding:"required"`
		Password string `json:"password" binding:"required"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Неверные данные"})
		return
	}

	var user models.User
	if err := database.DB.Where("email = ? AND category = 'admin'", input.Username).First(&user).Error; err != nil {
		c.JSON(http.StatusUnauthorized, gin.H{"error": "Неверные учетные данные"})
		return
	}

	if err := bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(input.Password)); err != nil {
		c.JSON(http.StatusUnauthorized, gin.H{"error": "Неверные учетные данные"})
		return
	}

	// Создаем JWT токен для администратора
	token, err := utils.GenerateToken(user.ID, true) // true указывает на то, что это админ
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при создании токена"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"success": true,
		"token":   token,
	})
}

// GetPendingAds возвращает список необработанных объявлений с полной информацией
func GetPendingAds(c *gin.Context) {
	var pendingAds []gin.H

	// Получаем объявления блогеров
	var bloggers []models.PostBlogger
	database.DB.Where("status = ?", "false").Find(&bloggers)
	for _, b := range bloggers {
		var user models.User
		database.DB.First(&user, b.UserID)
		
		pendingAds = append(pendingAds, gin.H{
			"id":               b.ID,
			"type":            "blogger",
			"user_name":       b.Nickname,
			"user_email":      user.Email,
			"user_phone":      user.Phone,
			"user_telegram":   user.Telegram,
			"category":        b.Category,
			"direction":       b.UserDirection,
			"photo":           b.PhotoBase64,
			"ad_comment":      b.AdComment,
			"instagram_link":  b.InstagramLink,
			"telegram_link":   b.TelegramLink,
			"youtube_link":    b.YoutubeLink,
			"created_at":      b.CreatedAt,
		})
	}

	// Получаем объявления компаний
	var companies []models.Company
	database.DB.Where("status = ?", "false").Find(&companies)
	for _, comp := range companies {
		var user models.User
		database.DB.First(&user, comp.UserID)
		
		pendingAds = append(pendingAds, gin.H{
			"id":               comp.ID,
			"type":            "company",
			"user_name":       comp.Name,
			"user_email":      user.Email,
			"user_phone":      user.Phone,
			"user_telegram":   user.Telegram,
			"category":        comp.Category,
			"direction":       comp.Direction,
			"photo":          comp.PhotoBase64,
			"budget":         comp.Budget,
			"ad_comment":      comp.AdComment,
			"website_link":    comp.WebsiteLink,
			"instagram_link":  comp.InstagramLink,
			"telegram_link":   comp.TelegramLink,
			"created_at":      comp.CreatedAt,
		})
	}

	// Получаем объявления фрилансеров
	var freelancers []models.Freelancer
	database.DB.Where("status = ?", "false").Find(&freelancers)
	for _, f := range freelancers {
		var user models.User
		database.DB.First(&user, f.UserID)
		
		pendingAds = append(pendingAds, gin.H{
			"id":               f.ID,
			"type":            "freelancer",
			"user_name":       f.Name,
			"user_email":      user.Email,
			"user_phone":      user.Phone,
			"user_telegram":   user.Telegram,
			"category":        f.Category,
			"photo":          f.PhotoBase64,
			"ad_comment":      f.AdComment,
			"github_link":     f.GithubLink,
			"portfolio_link":  f.PortfolioLink,
			"instagram_link":  f.InstagramLink,
			"telegram_link":   f.TelegramLink,
			"created_at":      f.CreatedAt,
		})
	}

	c.JSON(http.StatusOK, pendingAds)
}

// ApproveAd одобряет объявление
func ApproveAd(c *gin.Context) {
	var input struct {
		ID   uint   `json:"id"`
		Type string `json:"type"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Неверные данные"})
		return
	}

	var err error
	var userEmail string

	switch input.Type {
	case "blogger":
		var post models.PostBlogger
		if err = database.DB.First(&post, input.ID).Error; err == nil {
			post.Status = "true"
			err = database.DB.Save(&post).Error
			// Получаем email пользователя
			var user models.User
			database.DB.First(&user, post.UserID)
			userEmail = user.Email
		}
	case "company":
		var company models.Company
		if err = database.DB.First(&company, input.ID).Error; err == nil {
			company.Status = "true"
			err = database.DB.Save(&company).Error
			var user models.User
			database.DB.First(&user, company.UserID)
			userEmail = user.Email
		}
	case "freelancer":
		var freelancer models.Freelancer
		if err = database.DB.First(&freelancer, input.ID).Error; err == nil {
			freelancer.Status = "true"
			err = database.DB.Save(&freelancer).Error
			var user models.User
			database.DB.First(&user, freelancer.UserID)
			userEmail = user.Email
		}
	}

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обновлении статуса"})
		return
	}

	// Отправляем уведомление на email
	if userEmail != "" {
		utils.SendEmail(userEmail, "Объявление одобрено",
			"Ваше объявление было проверено и одобрено администратором.")
	}

	c.JSON(http.StatusOK, gin.H{"success": true})
}

// RejectAd отклоняет объявление
func RejectAd(c *gin.Context) {
	var input struct {
		ID     uint   `json:"id"`
		Type   string `json:"type"`
		Reason string `json:"reason"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		logger.LogError("RejectAd", err, "Invalid input data")
		c.JSON(http.StatusBadRequest, gin.H{"error": "Неверные данные"})
		return
	}

	var err error
	var userEmail string

	switch input.Type {
	case "blogger":
		var post models.PostBlogger
		if err = database.DB.First(&post, input.ID).Error; err == nil {
			err = database.DB.Delete(&post).Error
			var user models.User
			database.DB.First(&user, post.UserID)
			userEmail = user.Email
		}
	case "company":
		var company models.Company
		if err = database.DB.First(&company, input.ID).Error; err == nil {
			err = database.DB.Delete(&company).Error
			var user models.User
			database.DB.First(&user, company.UserID)
			userEmail = user.Email
		}
	case "freelancer":
		var freelancer models.Freelancer
		if err = database.DB.First(&freelancer, input.ID).Error; err == nil {
			err = database.DB.Delete(&freelancer).Error
			var user models.User
			database.DB.First(&user, freelancer.UserID)
			userEmail = user.Email
		}
	}

	if err != nil {
		logger.LogError("RejectAd", err, "Failed to delete ad")
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при отклонении объявления"})
		return
	}

	// Отправляем уведомление на email
	if userEmail != "" {
		message := fmt.Sprintf("Ваше объявление было отклонено администратором.\n\nПричина: %s", input.Reason)
		if err := utils.SendEmail(userEmail, "Объявление отклонено", message); err != nil {
			logger.LogError("RejectAd", err, "Failed to send email notification")
			// Продолжаем выполнение, даже если письмо не отправилось
		}
	}

	c.JSON(http.StatusOK, gin.H{"success": true})
}

// GetAdminStatistics возвращает статистику для админ-панели
func GetAdminStatistics(c *gin.Context) {
	var stats struct {
		PendingAds  int64 `json:"pending_ads"`
		ActiveUsers int64 `json:"active_users"`
		TotalAds    int64 `json:"total_ads"`
		TotalUsers  int64 `json:"total_users"`
	}

	// Подсчет необработанных объявлений
	database.DB.Model(&models.PostBlogger{}).Where("status = ?", "false").Count(&stats.PendingAds)
	var companyPending, freelancerPending int64
	database.DB.Model(&models.Company{}).Where("status = ?", "false").Count(&companyPending)
	database.DB.Model(&models.Freelancer{}).Where("status = ?", "false").Count(&freelancerPending)
	stats.PendingAds += companyPending + freelancerPending

	// Подсчет активных пользователей
	database.DB.Model(&models.User{}).Where("is_verified = ?", true).Count(&stats.ActiveUsers)

	// Общее количество объявлений
	database.DB.Model(&models.PostBlogger{}).Count(&stats.TotalAds)
	var totalCompany, totalFreelancer int64
	database.DB.Model(&models.Company{}).Count(&totalCompany)
	database.DB.Model(&models.Freelancer{}).Count(&totalFreelancer)
	stats.TotalAds += totalCompany + totalFreelancer

	// Общее количество пользователей
	database.DB.Model(&models.User{}).Count(&stats.TotalUsers)

	c.JSON(http.StatusOK, stats)
}

// GetAllUsers возвращает список всех пользователей с полной информацией
func GetAllUsers(c *gin.Context) {
	var users []models.User
	if err := database.DB.Find(&users).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при получении пользователей"})
		return
	}

	var response []gin.H
	for _, user := range users {
		// Получаем количество объявлений пользователя
		var bloggerCount, companyCount, freelancerCount int64
		database.DB.Model(&models.PostBlogger{}).Where("user_id = ?", user.ID).Count(&bloggerCount)
		database.DB.Model(&models.Company{}).Where("user_id = ?", user.ID).Count(&companyCount)
		database.DB.Model(&models.Freelancer{}).Where("user_id = ?", user.ID).Count(&freelancerCount)

		response = append(response, gin.H{
			"id":              user.ID,
			"name":            user.Name,
			"email":           user.Email,
			"phone":           user.Phone,
			"telegram":        user.Telegram,
			"category":        user.Category,
			"direction":       user.Direction,
			"status":          user.IsVerified,
			"created_at":      user.CreatedAt,
			"posts_count": gin.H{
				"blogger":     bloggerCount,
				"company":     companyCount,
				"freelancer":  freelancerCount,
			},
		})
	}

	c.JSON(http.StatusOK, response)
}

// ToggleUserStatus изменяет статус пользователя
func ToggleUserStatus(c *gin.Context) {
	var input struct {
		ID uint `json:"id"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Неверные данные"})
		return
	}

	var user models.User
	if err := database.DB.First(&user, input.ID).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Пользователь не найден"})
		return
	}

	user.IsVerified = !user.IsVerified
	if err := database.DB.Save(&user).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при обновлении статуса"})
		return
	}

	// Отправляем уведомление пользователю
	status := "активирован"
	if !user.IsVerified {
		status = "деактивирован"
	}
	utils.SendEmail(user.Email, "Изменение статуса аккаунта",
		"Ваш аккаунт был "+status+" администратором.")

	c.JSON(http.StatusOK, gin.H{"success": true})
}
