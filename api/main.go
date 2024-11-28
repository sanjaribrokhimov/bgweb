package main

import (
	"bloger_agencyBackend/database"
	"bloger_agencyBackend/handlers"
	"bloger_agencyBackend/middleware"
	"bloger_agencyBackend/utils"
	"log"
	"time"

	"github.com/gin-gonic/gin"
)

func main() {
	// Установка режима Gin
	gin.SetMode(gin.ReleaseMode)

	r := gin.Default()

	// CORS должен быть первым middleware
	r.Use(middleware.CORSMiddleware())

	// Настройка для обработки больших JSON
	r.MaxMultipartMemory = 8 << 20 // 8 MiB

	// Инициализация базы данных
	db := database.InitDB()
	sqlDB, err := db.DB()
	if err != nil {
		log.Fatal("Failed to get database instance:", err)
	}

	// Проверка подключения к базе данных
	err = sqlDB.Ping()
	if err != nil {
		log.Fatal("Failed to ping database:", err)
	}

	log.Println("Successfully connected to database")

	// Инициализируем логгер
	logger := utils.NewLogger()

	// Запускаем очистку старых логов каждую неделю
	go func() {
		for {
			logger.CleanOldLogs()
			// Ждем неделю перед следующей проверкой
			time.Sleep(7 * 24 * time.Hour)
		}
	}()

	// API маршруты
	api := r.Group("/api")
	{
		// Маршруты аутентификации
		auth := api.Group("/auth")
		{
			auth.POST("/register", handlers.Register)
			auth.POST("/login", handlers.Login)
			auth.POST("/verify-otp", handlers.VerifyOTP)
			auth.GET("/user", handlers.GetUserByEmail)
			auth.GET("/user/:id", handlers.GetUserByID)
			auth.POST("/resend-otp", handlers.ResendOTP)
		}

		// Маршруты для постов блогеров
		api.POST("/post-bloggers", handlers.CreatePostBlogger)
		api.GET("/post-bloggers", handlers.GetPostBloggers)
		api.GET("/post-bloggers/:id", handlers.GetPostBloggerByID)
		api.GET("/user-posts/:user_id", handlers.GetUserPosts)

		// Маршруты для компаний
		api.POST("/companies", handlers.CreateCompany)
		api.GET("/companies", handlers.GetCompanies)
		api.GET("/companies/:id", handlers.GetCompanyByID)
		api.GET("/user-companies/:user_id", handlers.GetUserCompanies)

		// Маршруты для фрилансеров
		api.POST("/freelancers", handlers.CreateFreelancer)
		api.GET("/freelancers", handlers.GetFreelancers)
		api.GET("/freelancers/:id", handlers.GetFreelancerByID)
		api.GET("/user-freelancers/:user_id", handlers.GetUserFreelancers)

		// Маршруты для объявлений
		ads := api.Group("/ads")
		{
			ads.GET("", handlers.GetAllAds)
			ads.GET("/category/:category", handlers.GetAdsByCategory)
			ads.GET("/details/:type/:id", handlers.GetAdDetails)
			ads.DELETE("/:type/:id", handlers.DeleteAd)
			ads.GET("/user/:category/:user_id", handlers.GetUserAdsByCategory)
			ads.GET("/search", handlers.SearchAds)
		}

		// Маршруты для уведомлений
		api.POST("/notifications/accept", handlers.CreateAcceptNotification)
		api.GET("/notifications/:user_id", handlers.GetUserNotifications)
		api.PUT("/notifications/:id/read", handlers.MarkNotificationAsRead)

		// WebSocket endpoint
		api.GET("/ws/:user_id", handlers.HandleWebSocket)
	}

	// Запуск сервера
	log.Println("Server starting on port 8888...")
	if err := r.Run(":8888"); err != nil {
		log.Fatal("Failed to start server:", err)
	}
}
