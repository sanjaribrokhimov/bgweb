package models

import (
	"gorm.io/gorm"
)

type User struct {
	gorm.Model
	Name       string `json:"name"`
	Email      string `json:"email" gorm:"unique"`
	Password   string `json:"password"`
	Phone      string `json:"phone"`
	Category   string `json:"category"`
	Direction  string `json:"direction"`
	Telegram   string `json:"telegram"`
	OTPCode    string `json:"otp_code"`
	IsVerified bool   `json:"is_verified" gorm:"default:false"`
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
