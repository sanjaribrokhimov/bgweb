package database

import (
	"fmt"
	"log"
	"os"

	"github.com/joho/godotenv"
	"gorm.io/driver/postgres"
	"gorm.io/gorm"
	"bloger_agencyBackend/models"
)

var DB *gorm.DB

func InitDB() *gorm.DB {
	// Загружаем .env файл
	err := godotenv.Load()
	if err != nil {
		log.Fatal("Error loading .env file")
	}

	dbHost := os.Getenv("DB_HOST")
	dbPort := os.Getenv("DB_PORT")
	dbUser := os.Getenv("DB_USER")
	dbPassword := os.Getenv("DB_PASSWORD")
	dbName := os.Getenv("DB_NAME")

	// Проверяем, что все необходимые переменные окружения установлены
	if dbHost == "" || dbPort == "" || dbUser == "" || dbPassword == "" || dbName == "" {
		log.Fatal("Database configuration variables are not set properly")
	}

	// Выводим DSN для отладки (закомментируйте в продакшене)
	dsn := fmt.Sprintf("host=%s port=%s user=%s password=%s dbname=%s sslmode=disable",
		dbHost, dbPort, dbUser, dbPassword, dbName)
	log.Printf("Attempting to connect with DSN: host=%s port=%s user=%s dbname=%s",
		dbHost, dbPort, dbUser, dbName)

	db, err := gorm.Open(postgres.Open(dsn), &gorm.Config{})
	if err != nil {
		log.Printf("Database connection error: %v", err)
		log.Fatal("Failed to connect to database:", err)
	}

	log.Println("Successfully connected to database")

	// Автомиграция моделей
	log.Println("Starting auto-migration...")
	err = db.AutoMigrate(
		&models.User{},
		&models.Ad{},
		&models.Freelancer{},
		&models.Company{},
		&models.PostBlogger{},
		&models.Notification{},
	)
	if err != nil {
		log.Printf("Auto-migration error: %v", err)
		log.Fatal("Failed to perform auto migration:", err)
	}
	log.Println("Auto-migration completed successfully")

	// Добавляем после успешного подключения к базе данных
	if err := db.Exec("CREATE EXTENSION IF NOT EXISTS pg_trgm;").Error; err != nil {
		log.Printf("Warning: Failed to create pg_trgm extension: %v", err)
	}

	DB = db
	return db
} 