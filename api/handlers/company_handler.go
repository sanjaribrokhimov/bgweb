package handlers

import (
	"bloger_agencyBackend/database"
	"bloger_agencyBackend/models"
	"log"
	"net/http"

	"github.com/gin-gonic/gin"
)

func CreateCompany(c *gin.Context) {
	var company models.Company

	// Используем метод Gin для привязки JSON
	if err := c.ShouldBindJSON(&company); err != nil {
		log.Printf("JSON binding error: %v", err)
		c.JSON(http.StatusBadRequest, gin.H{
			"error":   "Invalid JSON format",
			"details": err.Error(),
		})
		return
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

	c.JSON(http.StatusOK, companies)
}

func GetCompanyByID(c *gin.Context) {
	id := c.Param("id")
	var company models.Company

	if err := database.DB.First(&company, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Company not found"})
		return
	}

	c.JSON(http.StatusOK, company)
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
