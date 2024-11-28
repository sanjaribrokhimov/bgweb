package models

import (
	"gorm.io/gorm"
	"time"
)

type User struct {
	ID          uint      `json:"id" gorm:"primaryKey"`
	Name        string    `json:"name" gorm:"not null"`
	Email       string    `json:"email" gorm:"unique;not null"`
	Password    string    `json:"password" gorm:"not null"`
	Phone       string    `json:"phone" gorm:"not null"`
	Category    string    `json:"category" gorm:"not null"`
	OTPCode     string    `json:"otp_code"`
	IsVerified  bool      `json:"is_verified" gorm:"default:false"`
	CreatedAt   time.Time `json:"created_at"`
	UpdatedAt   time.Time `json:"updated_at"`
}

type Like struct {
	gorm.Model
	UserID        uint
	PostBloggerID uint
	IsLiked       bool
}

type History struct {
	gorm.Model
	UserID        uint
	PostBloggerID uint
	Action        string
}
