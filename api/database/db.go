package database

import (
	"fmt"
	"log"
	"os"
	"bloger_agencyBackend/models"
	"gorm.io/driver/postgres"
	"gorm.io/gorm"
	"github.com/joho/godotenv"
)

var DB *gorm.DB

func InitDB() *gorm.DB {
	// Загружаем .env файл
	if err := godotenv.Load(); err != nil {
		log.Fatal("Error loading .env file")
	}

	// Проверяем наличие переменных окружения
	dbHost := os.Getenv("DB_HOST")
	dbPort := os.Getenv("DB_PORT")
	dbUser := os.Getenv("DB_USER")
	dbPass := os.Getenv("DB_PASSWORD")
	dbName := os.Getenv("DB_NAME")

	if dbHost == "" || dbPort == "" || dbUser == "" || dbPass == "" || dbName == "" {
		log.Fatal("Database environment variables are not set")
	}

	dsn := fmt.Sprintf("host=%s user=%s password=%s dbname=%s port=%s sslmode=disable",
		dbHost, dbUser, dbPass, dbName, dbPort)

	db, err := gorm.Open(postgres.Open(dsn), &gorm.Config{})
	if err != nil {
		log.Fatal("Failed to connect to database:", err)
	}

	DB = db // Сохраняем подключение в глобальной переменной

	// Миграция моделей
	if err := DB.AutoMigrate(
		&models.User{},
		&models.PostBlogger{},
		&models.Company{},
		&models.Freelancer{},
		&models.Notification{},
		&models.UserAgreement{},
	); err != nil {
		log.Fatal("Failed to migrate database:", err)
	}

	return DB
}
