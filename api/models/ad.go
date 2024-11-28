package models

import (
    "gorm.io/gorm"
)

type Ad struct {
    gorm.Model
    UserID      uint    `json:"user_id"`
    Category    string  `json:"category"`
    Name        string  `json:"name"`
    PhotoBase64 string  `json:"photo_base64"`
    Price       float64 `json:"price,omitempty"`
    Budget      float64 `json:"budget,omitempty"`
    AdComment   string  `json:"ad_comment,omitempty"`
    Status      string  `json:"status" gorm:"default:'active'"`
} 