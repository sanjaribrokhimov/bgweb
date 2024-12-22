package models

import (
    "gorm.io/gorm"
    "time"
)

type Freelancer struct {
    gorm.Model
    UserID      uint    `json:"user_id"`
    Name        string  `json:"name" binding:"required"`
    Category    string  `json:"category" binding:"required"`
    PhotoBase64 string  `json:"photo_base64" binding:"required"`
    AdComment   string  `json:"ad_comment,omitempty"`
    
    // Социальные сети
    GithubLink    string `json:"github_link,omitempty"`
    PortfolioLink string `json:"portfolio_link,omitempty"`
    InstagramLink string `json:"instagram_link,omitempty"`
    TelegramLink  string `json:"telegram_link,omitempty"`
    YoutubeLink   string `json:"youtube_link,omitempty"`
    Status        string `json:"status" gorm:"default:'false'"`
    Description   string `json:"description"`
    Skills        string `json:"skills"`
    Experience    string `json:"experience"`
    CreatedAt     time.Time `json:"created_at"`
} 