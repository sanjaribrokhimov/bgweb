package models

import (
	"gorm.io/gorm"
)

type Admin struct {
	gorm.Model
	Username string `json:"username" gorm:"unique"`
	Password string `json:"password"`
	Email    string `json:"email"`
} 