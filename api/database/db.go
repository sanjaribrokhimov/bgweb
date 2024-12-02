package database

import (
	"fmt"
	"log"
	"os"
	"time"

	"bloger_agencyBackend/models"

	"github.com/joho/godotenv"
	"gorm.io/driver/postgres"
	"gorm.io/gorm"
)

var DB *gorm.DB

func InitDB() *gorm.DB {
	// Очищаем все переменные окружения DB_* перед началом
	os.Unsetenv("DB_HOST")
	os.Unsetenv("DB_PORT")
	os.Unsetenv("DB_USER")
	os.Unsetenv("DB_PASSWORD")
	os.Unsetenv("DB_NAME")

	// Показываем текущую директорию
	dir, _ := os.Getwd()
	log.Printf("Current directory: %s", dir)

	// Пробуем загрузить .env файл
	if err := godotenv.Load(); err != nil {
		log.Fatal("Error loading .env file:", err)
	}

	// Сразу проверим значение DB_USER
	log.Printf("DB_USER immediately after loading .env: %s", os.Getenv("DB_USER"))

	dbHost := os.Getenv("DB_HOST")
	dbPort := os.Getenv("DB_PORT")
	dbUser := os.Getenv("DB_USER")
	dbPassword := os.Getenv("DB_PASSWORD")
	dbName := os.Getenv("DB_NAME")

	// Проверяем значения после присваивания
	log.Printf("Values after assignment:")
	log.Printf("dbHost: %s", dbHost)
	log.Printf("dbPort: %s", dbPort)
	log.Printf("dbUser: %s", dbUser)
	log.Printf("dbName: %s", dbName)

	// Проверяем, что все необходимые переменные окружения установлены
	if dbHost == "" || dbPort == "" || dbUser == "" || dbPassword == "" || dbName == "" {
		log.Fatal("Database configuration variables are not set properly")
	}

	// Выводим значения DSN для отладки
	dsn := fmt.Sprintf("host=%s port=%s user=%s password=%s dbname=%s sslmode=disable",
		dbHost, dbPort, dbUser, dbPassword, dbName)
	log.Printf("Full DSN string: %s", dsn)

	// Несколько попыток подключения
	var (
		db  *gorm.DB
		err error
	)
	
	for i := 0; i < 3; i++ {
		db, err = gorm.Open(postgres.Open(dsn), &gorm.Config{})
		if err == nil {
			break
		}
		log.Printf("Attempt %d: Failed to connect to database: %v", i+1, err)
		time.Sleep(2 * time.Second)
	}

	if err != nil {
		log.Fatal("Failed to connect to database after multiple attempts:", err)
	}

	log.Println("Successfully connected to database")

	// Автомиграция моделей
	log.Println("Starting auto-migration...")
	if err := db.AutoMigrate(
		&models.User{},
		&models.Ad{},
		&models.Freelancer{},
		&models.Company{},
		&models.PostBlogger{},
		&models.Notification{},
	); err != nil {
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
