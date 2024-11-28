package handlers

import (
    "net/http"
    "github.com/gin-gonic/gin"
    "bloger_agencyBackend/database"
    "bloger_agencyBackend/models"
    "log"
)

func CreatePostBlogger(c *gin.Context) {
    var post models.PostBlogger
    
    if err := c.ShouldBindJSON(&post); err != nil {
        log.Printf("JSON binding error: %v", err)
        c.JSON(http.StatusBadRequest, gin.H{
            "error": "Invalid JSON format",
            "details": err.Error(),
        })
        return
    }

    // Проверяем обязательные поля
    if post.Nickname == "" || post.Category == "" || post.TelegramUsername == "" {
        c.JSON(http.StatusBadRequest, gin.H{"error": "Missing required fields"})
        return
    }

    // Проверяем количество постов пользователя
    var count int64
    if err := database.DB.Model(&models.PostBlogger{}).Where("user_id = ?", post.UserID).Count(&count).Error; err != nil {
        log.Printf("Database error when checking posts count: %v", err)
        c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to check user posts"})
        return
    }

    if count >= 3 {
        c.JSON(http.StatusBadRequest, gin.H{"error": "Maximum number of posts (3) reached for this user"})
        return
    }

    // Создаем пост
    if err := database.DB.Create(&post).Error; err != nil {
        log.Printf("Database error when creating post: %v", err)
        c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create post"})
        return
    }

    c.JSON(http.StatusOK, gin.H{
        "message": "Post created successfully",
        "post": post,
        "posts_count": count + 1,
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