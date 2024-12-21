package router

import (
	"github.com/gin-gonic/gin"
	"bloger_agencyBackend/handlers"
)

func SetupRouter(router *gin.Engine) {
	auth := router.Group("/api/auth")
	{
		auth.POST("/register", handlers.Register)
		auth.POST("/login", handlers.Login)
		auth.POST("/verify-otp", handlers.VerifyOTP)
		auth.POST("/resend-otp", handlers.ResendOTP)
		auth.POST("/forgot-password", handlers.ForgotPassword)
		auth.POST("/reset-password", handlers.ResetPassword)
	}

	users := router.Group("/api/users")
	{
		users.GET("/profile", handlers.GetUserProfile)
		users.PUT("/profile", handlers.UpdateUserProfile)
	}
} 