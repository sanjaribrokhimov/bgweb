package handlers

import (
	"bloger_agencyBackend/database"
	"bloger_agencyBackend/models"
	"log"
	"net/http"

	"github.com/gin-gonic/gin"
)

type CompanyInput struct {
	UserID           uint   `json:"user_id"`
	Name             string `json:"name"`
	Category         string `json:"category"`
	Direction        string `json:"direction"`
	PhotoBase64      string `json:"photo_base64"`
	Budget           int    `json:"budget"`
	AdComment        string `json:"ad_comment"`
	WebsiteLink      string `json:"website_link"`
	InstagramLink    string `json:"instagram_link"`
	TelegramLink     string `json:"telegram_link"`
	TelegramUsername string `json:"telegram_username"`
}

func CreateCompany(c *gin.Context) {
	var input CompanyInput
	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	company := models.Company{
		UserID:           input.UserID,
		Name:             input.Name,
		Category:         input.Category,
		Direction:        input.Direction,
		PhotoBase64:      input.PhotoBase64,
		Budget:           input.Budget,
		AdComment:        input.AdComment,
		WebsiteLink:      input.WebsiteLink,
		InstagramLink:    input.InstagramLink,
		TelegramLink:     input.TelegramLink,
		TelegramUsername: input.TelegramUsername,
		Status:           "active",
	}

	// Проверяем обязательные поля
	if company.Name == "" || company.Category == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Missing required fields"})
		return
	}

	// Проверяем бюджет
	if company.Budget <= 0 {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Budget is required"})
		return
	}

	// Проверяем количество постов пользователя
	var count int64
	if err := database.DB.Model(&models.Company{}).Where("user_id = ?", company.UserID).Count(&count).Error; err != nil {
		log.Printf("Database error when checking companies count: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to check user companies"})
		return
	}

	if count >= 3 {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Maximum number of companies (3) reached for this user"})
		return
	}

	// Создаем компанию
	if err := database.DB.Create(&company).Error; err != nil {
		log.Printf("Database error when creating company: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create company"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message":         "Company created successfully",
		"company":         company,
		"companies_count": count + 1,
	})
}

func GetCompanies(c *gin.Context) {
	var companies []models.Company

	if err := database.DB.Find(&companies).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch companies"})
		return
	}

	// Форматируем ответ для каждой компании
	formattedCompanies := make([]gin.H, len(companies))
	for i, company := range companies {
		formattedCompanies[i] = gin.H{
			"ID":                company.ID,
			"name":              company.Name,
			"category":          company.Category,
			"photo_base64":      company.PhotoBase64,
			"budget":            company.Budget,
			"ad_comment":        company.AdComment,
			"website_link":      company.WebsiteLink,
			"instagram_link":    company.InstagramLink,
			"telegram_link":     company.TelegramLink,
			"telegram_username": company.TelegramUsername,
			"user_id":           company.UserID,
		}
	}

	c.JSON(http.StatusOK, formattedCompanies)
}

func GetCompanyByID(c *gin.Context) {
	id := c.Param("id")
	var company models.Company

	if err := database.DB.First(&company, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Company not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"ID":                company.ID,
		"name":              company.Name,
		"category":          company.Category,
		"photo_base64":      company.PhotoBase64,
		"budget":            company.Budget,
		"ad_comment":        company.AdComment,
		"website_link":      company.WebsiteLink,
		"instagram_link":    company.InstagramLink,
		"telegram_link":     company.TelegramLink,
		"telegram_username": company.TelegramUsername,
		"user_id":           company.UserID,
	})
}

func GetUserCompanies(c *gin.Context) {
	userID := c.Param("user_id")
	var companies []models.Company

	if err := database.DB.Where("user_id = ?", userID).Find(&companies).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch user companies"})
		return
	}

	c.JSON(http.StatusOK, companies)
}
