package handlers

import (
	"bloger_agencyBackend/database"
	"bloger_agencyBackend/models"
	"bloger_agencyBackend/utils"
	"bytes"
	"fmt"
	"io"
	"log"
	"net/http"
	"time"
	"github.com/gin-gonic/gin"
)



func CreateAcceptNotification(c *gin.Context) {
	var input struct {
		FromUserID uint   `json:"from_user_id"`
		ToUserID   uint   `json:"to_user_id"`
		AdID       uint   `json:"ad_id"`
		AdType     string `json:"ad_type"`
		UserMessage    string `json:"user_message"` 
	}

	// Логируем входящие данные ДО привязки JSON
	body, _ := io.ReadAll(c.Request.Body)
	log.Printf("Raw request body: %s", string(body))
	c.Request.Body = io.NopCloser(bytes.NewBuffer(body))

	if err := c.ShouldBindJSON(&input); err != nil {
		log.Printf("Error binding JSON: %v", err)
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	log.Printf("Processing notification for ad type: %s, ad ID: %d", input.AdType, input.AdID)

	// Получаем данные отправителя
	var fromUser models.User
	if err := database.DB.First(&fromUser, input.FromUserID).Error; err != nil {
		log.Printf("Error getting sender info: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to get sender info"})
		return
	}

	// Получаем данные получателя
	var toUser models.User
	if err := database.DB.First(&toUser, input.ToUserID).Error; err != nil {
		log.Printf("Error getting recipient info: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to get recipient info"})
		return
	}

	// Проверяем существование объявления фрилансера
	if input.AdType == "freelancer" {
		var freelancer models.Freelancer
		if err := database.DB.First(&freelancer, input.AdID).Error; err != nil {
			log.Printf("Error finding freelancer ad: %v", err)
			c.JSON(http.StatusNotFound, gin.H{"error": "Freelancer ad not found"})
			return
		}
		log.Printf("Found freelancer ad: %+v", freelancer)
	}

	// Создаем уведомление в базе
	notification := models.Notification{
		FromUserID: input.FromUserID,
		ToUserID:   input.ToUserID,
		AdID:       input.AdID,
		AdType:     input.AdType,
		Type:       "accept",
		Message:    "У вас новое соглашение по вашему объявлению",
		MessageToUser:  input.UserMessage,
		IsRead:     false,
	}

	if err := database.DB.Create(&notification).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create notification"})
		return
	}

	emailText := fmt.Sprintf(
		"У вас новое соглашение по вашему объявлению!\n\n"+
			"Сообщение от пользователя:\n%s\n\n"+  // Добавляем блок с сообщением
			"Данные пользователя:\n"+
			"Имя: %s\n"+
			"Категория: %s\n"+
			"Направление: %s\n"+
			"Email: %s\n"+
			"Telegram: %s\n"+
			"Instagram: %s\n\n"+
			"Пожалуйста, свяжитесь с пользователем через Telegram или Email для обсуждения деталей.",
		input.UserMessage,  // Добавляем пользовательское сообщение первым аргументом
		fromUser.Name,
		fromUser.Category,
		fromUser.Direction,
		fromUser.Email,
		fromUser.Telegram,
		fromUser.Instagram,
	)

	// Отправляем email
	err := utils.SendEmail(toUser.Email, "Новое соглашение по объявлению", emailText)
	if err != nil {
		log.Printf("Failed to send email: %v", err)
		// Не возвращаем ошибку клиенту, так как уведомление уже создано
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
			"user_message": notification.MessageToUser,
		}


		// Поучаем детали объявления
		var adDetails interface{}
		switch notification.AdType {
		case "blogger":
			var post models.PostBlogger
			if err := database.DB.Where("id = ?", notification.AdID).First(&post).Error; err == nil {
				adDetails = post
			} else {
				log.Printf("Error fetching blogger post: %v", err)
			}
		case "company":
			var company models.Company
			if err := database.DB.Where("id = ?", notification.AdID).First(&company).Error; err == nil {
				adDetails = company
			} else {
				log.Printf("Error fetching company: %v", err)
			}
		case "freelancer":
			var freelancer models.Freelancer
			if err := database.DB.Where("id = ?", notification.AdID).First(&freelancer).Error; err == nil {
				adDetails = freelancer
			} else {
				log.Printf("Error fetching freelancer: %v", err)
			}
		}

		if adDetails != nil {
			enriched["ad_details"] = adDetails
			enriched["ad_id"] = notification.AdID
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

func ClearOldNotifications() {
	oneMonthAgo := time.Now().AddDate(0, 0, -5)
	if err := database.DB.Where("created_at < ?", oneMonthAgo).Delete(&models.Notification{}).Error; err != nil {
		log.Printf("Ошибка при очистке старых уведомлений: %v", err)
	} else {
		log.Println("Старые уведомления успешно удалены.")
	}
}

func StartNotificationCleaner() {
	// Сразу запускаем очистку при старте
	log.Println("Первый запуск очистки уведомлений...")
	ClearOldNotifications()

	// Запускаем таймер на 24 часа
	ticker := time.NewTicker(24 * time.Hour)
	defer ticker.Stop()

	for range ticker.C {
		log.Println("Запуск очистки уведомлений...")
		ClearOldNotifications()
	}
}
