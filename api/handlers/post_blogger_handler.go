package handlers

import (
	"bloger_agencyBackend/database"
	"bloger_agencyBackend/models"
	"log"
	"net/http"

	"github.com/gin-gonic/gin"
)

func CreatePostBlogger(c *gin.Context) {
	var input struct {
		UserID           int    `json:"user_id"`
		Name             string `json:"name"`
		PhotoBase64      string `json:"photo_base64"`
		LookingFor       string `json:"looking_for"`
		Category         string `json:"category"`
		Direction        string `json:"direction"`
		TelegramUsername string `json:"telegram_username"`
		InstagramLink    string `json:"instagram_link"`
		TelegramLink     string `json:"telegram_link"`
		YoutubeLink      string `json:"youtube_link"`
		Status           string `json:"status"`
	}

	if err := c.ShouldBindJSON(&input); err != nil {
		log.Printf("Ошибка привязки JSON: %v", err)
		log.Printf("Полученные данные: %+v", input)
		c.JSON(http.StatusBadRequest, gin.H{"error": "Некорректные данные"})
		return
	}

	// Проверяем обязательные поля
	if input.Name == "" || input.PhotoBase64 == "" || input.LookingFor == "" ||
		input.Category == "" || input.TelegramUsername == "" {
		log.Printf("Отсутствуют обязательные поля: name=%s, photo=%v, looking_for=%s, category=%s, telegram=%s",
			input.Name, len(input.PhotoBase64) > 0, input.LookingFor, input.Category, input.TelegramUsername)
		c.JSON(http.StatusBadRequest, gin.H{"error": "Заполните все обязательные поля"})
		return
	}

	// Создаем запись в базе данных
	postBlogger := models.PostBlogger{
		UserID:           uint(input.UserID),
		Nickname:         input.Name,
		PhotoBase64:      input.PhotoBase64,
		AdComment:        input.LookingFor,
		Category:         input.Category,
		UserDirection:    input.Direction,
		TelegramUsername: input.TelegramUsername,
		InstagramLink:    input.InstagramLink,
		TelegramLink:     input.TelegramLink,
		YoutubeLink:      input.YoutubeLink,
		Status:           "false",
	}

	if err := database.DB.Create(&postBlogger).Error; err != nil {
		log.Printf("Ошибка создания записи в БД: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при создании поста"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message": "Post created successfully",
		"post":    postBlogger,
	})
}

func GetPostBloggers(c *gin.Context) {
	var posts []models.PostBlogger

	if err := database.DB.Find(&posts).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch posts"})
		return
	}

	c.JSON(http.StatusOK, posts)
}

func GetPostBloggerByID(c *gin.Context) {
	id := c.Param("id")
	var post models.PostBlogger

	if err := database.DB.First(&post, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Post not found"})
		return
	}

	c.JSON(http.StatusOK, post)
}

func GetUserPosts(c *gin.Context) {
	userID := c.Param("user_id")
	var posts []models.PostBlogger

	if err := database.DB.Where("user_id = ?", userID).Find(&posts).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch user posts"})
		return
	}

	c.JSON(http.StatusOK, posts)
}
