package models

import (
	"time"
)

type PostBlogger struct {
	ID               uint      `json:"id" gorm:"primaryKey"`
	UserID           uint      `json:"user_id"`
	Nickname         string    `json:"nickname"`
	PhotoBase64      string    `json:"photo_base64"`
	AdComment        string    `json:"ad_comment"`
	Category         string    `json:"category"`
	UserDirection    string    `json:"direction"`
	TelegramUsername string    `json:"telegram_username"`
	InstagramLink    string    `json:"instagram_link"`
	TelegramLink     string    `json:"telegram_link"`
	YoutubeLink      string    `json:"youtube_link"`
	CreatedAt        time.Time `json:"created_at"`
	UpdatedAt        time.Time `json:"updated_at"`
	Status           string    `json:"status" gorm:"default:'false'"`
}
