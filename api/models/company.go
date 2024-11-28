package models

import (
	"gorm.io/gorm"
)

type Company struct {
	gorm.Model
	UserID      uint   `json:"user_id"`
	Name        string `json:"name" binding:"required"`
	Category    string `json:"category" binding:"required"`
	PhotoBase64 string `json:"photo_base64" binding:"required"`
	Budget      float64 `json:"budget"`
	AdComment   string  `json:"ad_comment,omitempty"`
	
	// Социальные сети
	WebsiteLink   string `json:"website_link,omitempty"`
	InstagramLink string `json:"instagram_link,omitempty"`
	TelegramLink  string `json:"telegram_link,omitempty"`
	
	Status string `json:"status" gorm:"default:'active'"`
}
