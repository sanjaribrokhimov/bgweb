package models

import (
    "gorm.io/gorm"
)

type PostBlogger struct {
    gorm.Model
    UserID           uint    `json:"user_id"`
    Nickname         string  `json:"nickname" binding:"required"`
    Category         string  `json:"category" binding:"required"`
    PhotoBase64      string  `json:"photo_base64" binding:"required"`
    Followers        int     `json:"followers" binding:"required"`
    Engagement       float64 `json:"engagement" binding:"required"`
    TelegramUsername string  `json:"telegram_username" binding:"required"`
    AdComment        string  `json:"ad_comment,omitempty"`
    
    // Социальные сети
    InstagramLink string `json:"instagram_link,omitempty"`
    TelegramLink  string `json:"telegram_link,omitempty"`
    YoutubeLink   string `json:"youtube_link,omitempty"`
    TiktokLink    string `json:"tiktok_link,omitempty"`
    
    Status string `json:"status" gorm:"default:'active'"`
} 