package handlers

import (
	"net/http"
	"github.com/gin-gonic/gin"
	"bloger_agencyBackend/database"
	"bloger_agencyBackend/models"
)

// Проверка существующих согласий
func CheckUserAgreements(c *gin.Context) {
	userID := c.Query("user_id")
	if userID == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "User ID is required"})
		return
	}

	var agreements []models.UserAgreement
	if err := database.DB.Where("user_id = ?", userID).Find(&agreements).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch agreements"})
		return
	}

	c.JSON(http.StatusOK, agreements)
} 