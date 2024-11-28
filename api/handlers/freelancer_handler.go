package handlers

import (
    "net/http"
    "github.com/gin-gonic/gin"
    "bloger_agencyBackend/database"
    "bloger_agencyBackend/models"
    "log"
)

func CreateFreelancer(c *gin.Context) {
    var freelancer models.Freelancer
    
    if err := c.ShouldBindJSON(&freelancer); err != nil {
        log.Printf("JSON binding error: %v", err)
        c.JSON(http.StatusBadRequest, gin.H{
            "error": "Invalid JSON format",
            "details": err.Error(),
        })
        return
    }

    // Проверяем обязательные поля
    if freelancer.Name == "" || freelancer.Category == "" {
        c.JSON(http.StatusBadRequest, gin.H{"error": "Missing required fields"})
        return
    }

    // Проверяем количество постов пользователя
    var count int64
    if err := database.DB.Model(&models.Freelancer{}).Where("user_id = ?", freelancer.UserID).Count(&count).Error; err != nil {
        log.Printf("Database error when checking freelancers count: %v", err)
        c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to check user freelancers"})
        return
    }

    if count >= 3 {
        c.JSON(http.StatusBadRequest, gin.H{"error": "Maximum number of freelancer posts (3) reached for this user"})
        return
    }

    // Создаем запись фрилансера
    if err := database.DB.Create(&freelancer).Error; err != nil {
        log.Printf("Database error when creating freelancer: %v", err)
        c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create freelancer post"})
        return
    }

    c.JSON(http.StatusOK, gin.H{
        "message": "Freelancer post created successfully",
        "freelancer": freelancer,
        "freelancers_count": count + 1,
    })
}

func GetFreelancers(c *gin.Context) {
    var freelancers []models.Freelancer
    
    if err := database.DB.Find(&freelancers).Error; err != nil {
        c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch freelancers"})
        return
    }

    c.JSON(http.StatusOK, freelancers)
}

func GetFreelancerByID(c *gin.Context) {
    id := c.Param("id")
    var freelancer models.Freelancer

    if err := database.DB.First(&freelancer, id).Error; err != nil {
        c.JSON(http.StatusNotFound, gin.H{"error": "Freelancer not found"})
        return
    }

    c.JSON(http.StatusOK, freelancer)
}

func GetUserFreelancers(c *gin.Context) {
    userID := c.Param("user_id")
    var freelancers []models.Freelancer

    if err := database.DB.Where("user_id = ?", userID).Find(&freelancers).Error; err != nil {
        c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch user freelancers"})
        return
    }

    c.JSON(http.StatusOK, freelancers)
} 