package models

import (
	"gorm.io/gorm"
)

type Company struct {
	gorm.Model
	UserID           uint   `json:"user_id"`
	Name             string `json:"name"`
	Category         string `json:"category"`
	Direction        string `json:"direction"`
	PhotoBase64      string `json:"photo_base64"`
	Budget           int    `json:"budget"`
	AdComment        string `json:"ad_comment"`
	WebsiteLink      string `json:"website_link"`
	InstagramLink    string `json:"instagram_link"`
	TelegramLink     string `json:"telegram_link"`
	TelegramUsername string `json:"telegram_username"`
	Status           string `json:"status" gorm:"default:'active'"`
}
