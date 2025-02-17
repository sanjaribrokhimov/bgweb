package models

import (
	"gorm.io/gorm"
)

type User struct {
	gorm.Model
	Name       string `json:"name"`
	Email      string `json:"email" gorm:"uniqueIndex:idx_email,where:email <> ''"`
	Password   string `json:"password"`
	Phone      string `json:"phone"`
	Category   string `json:"category"`
	Direction  string `json:"direction"`
	Telegram   string `json:"telegram"`
	Instagram  string `json:"instagram"`
	OTPCode    string `json:"otp_code"`
	IsVerified bool   `json:"is_verified" gorm:"default:false"`
	TgChatID   string `json:"tg_chat_id" gorm:"default:null"`
	TgUserID   string `json:"tg_user_id" gorm:"default:null"`
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
