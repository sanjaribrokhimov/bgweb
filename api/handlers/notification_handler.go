package handlers

import (
	"bloger_agencyBackend/database"
	"bloger_agencyBackend/models"
	"bloger_agencyBackend/utils"
	"log"
	"net/http"

	"github.com/gin-gonic/gin"
)

func CreateAcceptNotification(c *gin.Context) {
	var input struct {
		FromUserID uint   `json:"from_user_id"` // ID пользователя, который отправляет уведомление
		ToUserID   uint   `json:"to_user_id"`   // ID пользователя, которому отправляется уведомление
		AdID       uint   `json:"ad_id"`        // ID объявления
		AdType     string `json:"ad_type"`      // тип объявления
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	// Создаем уведомление в базе
	notification := models.Notification{
		FromUserID: input.FromUserID,
		ToUserID:   input.ToUserID,
		AdID:       input.AdID,
		AdType:     input.AdType,
		Type:       "accept",
		Message:    "У вас новое соглашение по вашему объявлению",
		IsRead:     false,
	}

	if err := database.DB.Create(&notification).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create notification"})
		return
	}

	// Получаем email получателя
	var toUser models.User
	if err := database.DB.First(&toUser, input.ToUserID).Error; err != nil {
		log.Printf("Failed to find user for email notification: %v", err)
	} else {
		// Отправляем email
		err := utils.SendEmail(toUser.Email, "Новое соглашение",
			"У вас новое соглашение по вашему объявлению. Пожалуйста, проверьте уведомления в приложении.")
		if err != nil {
			log.Printf("Failed to send email: %v", err)
		}
	}

	// Отправляем уведомление через WebSocket
	SendWebSocketNotification(notification.ToUserID, notification)

	c.JSON(http.StatusOK, notification)
}

func GetUserNotifications(c *gin.Context) {
	userID := c.Param("user_id")
	var notifications []models.Notification

	// Получаем уведомления с информацией об отправителе и получателе
	if err := database.DB.Preload("FromUser").Preload("ToUser").
		Where("to_user_id = ?", userID).
		Order("created_at desc").
		Find(&notifications).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch notifications"})
		return
	}

	// Обогащаем каждое уведомление деталями объявления
	var enrichedNotifications []gin.H
	for _, notification := range notifications {
		enriched := gin.H{
			"id":         notification.ID,
			"from_user":  notification.FromUser,
			"to_user":    notification.ToUser,
			"type":       notification.Type,
			"message":    notification.Message,
			"is_read":    notification.IsRead,
			"created_at": notification.CreatedAt,
			"ad_type":    notification.AdType,
		}

		// Получаем детали объявления
		var adDetails interface{}
		switch notification.AdType {
		case "blogger":
			var post models.PostBlogger
			if err := database.DB.First(&post, notification.AdID).Error; err == nil {
				adDetails = post
			}
		case "company":
			var company models.Company
			if err := database.DB.First(&company, notification.AdID).Error; err == nil {
				adDetails = company
			}
		case "freelancer":
			var freelancer models.Freelancer
			if err := database.DB.First(&freelancer, notification.AdID).Error; err == nil {
				adDetails = freelancer
			}
		}

		if adDetails != nil {
			enriched["ad_details"] = adDetails
		}

		enrichedNotifications = append(enrichedNotifications, enriched)
	}

	c.JSON(http.StatusOK, enrichedNotifications)
}

func MarkNotificationAsRead(c *gin.Context) {
	notificationID := c.Param("id")

	if err := database.DB.Model(&models.Notification{}).Where("id = ?", notificationID).Update("is_read", true).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update notification"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Notification marked as read"})
}
