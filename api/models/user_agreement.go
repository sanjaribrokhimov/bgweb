package models

import "time"

type UserAgreement struct {
    ID        uint      `json:"id" gorm:"primaryKey"`
    UserID    int       `json:"user_id"`
    AdID      int       `json:"ad_id"`
    AdType    string    `json:"ad_type"`
    CreatedAt time.Time `json:"created_at"`
} 