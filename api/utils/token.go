package utils

import (
	"time"
	"github.com/golang-jwt/jwt"
)

var jwtKey = []byte("your_secret_key") // В продакшене используйте безопасный ключ из конфига

type Claims struct {
	UserID uint `json:"user_id"`
	IsAdmin bool `json:"is_admin"`
	jwt.StandardClaims
}

// GenerateToken создает JWT токен для пользователя
func GenerateToken(userID uint, isAdmin bool) (string, error) {
	expirationTime := time.Now().Add(24 * time.Hour)
	claims := &Claims{
		UserID: userID,
		IsAdmin: isAdmin,
		StandardClaims: jwt.StandardClaims{
			ExpiresAt: expirationTime.Unix(),
		},
	}

	token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	return token.SignedString(jwtKey)
}

// ValidateToken проверяет JWT токен
func ValidateToken(tokenStr string) (*Claims, error) {
	claims := &Claims{}
	token, err := jwt.ParseWithClaims(tokenStr, claims, func(token *jwt.Token) (interface{}, error) {
		return jwtKey, nil
	})

	if err != nil {
		return nil, err
	}

	if !token.Valid {
		return nil, jwt.ErrSignatureInvalid
	}

	return claims, nil
} 