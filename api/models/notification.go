package models

import (
	"gorm.io/gorm"
)

type Notification struct {
	gorm.Model
	FromUserID uint   `json:"from_user_id"`
	ToUserID   uint   `json:"to_user_id"`
	AdID       uint   `json:"ad_id"`
	AdType     string `json:"ad_type"`
	Type       string `json:"type"`
	Message    string `json:"message"`
	IsRead     bool   `json:"is_read" gorm:"default:false"`
	FromUser   User   `json:"from_user" gorm:"foreignKey:FromUserID"`
	ToUser     User   `json:"to_user" gorm:"foreignKey:ToUserID"`
}
