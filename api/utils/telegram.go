package utils

import (
	"fmt"
	"net/http"
)

// SendTelegramOTP отправляет OTP код через Telegram бот
func SendTelegramOTP(chatID string, otp string) error {
	botToken := "7690904808:AAEyzgbEZ3--sQ1pkJ-bFBpnDSCY2rNq9VY"

	// Используем HTML-форматирование для выделения кода
	message := fmt.Sprintf("<b>🔔 Ваш код подтверждения:</b> <code>%s</code>", otp)
	url := fmt.Sprintf("https://api.telegram.org/bot%s/sendMessage", botToken)

	// Создаем HTTP клиент
	client := &http.Client{}

	// Формируем запрос
	req, err := http.NewRequest("POST", url, nil)
	if err != nil {
		return fmt.Errorf("error creating request: %v", err)
	}

	// Добавляем параметры
	q := req.URL.Query()
	q.Add("chat_id", chatID)
	q.Add("text", message)
	q.Add("parse_mode", "HTML") // Указываем режим парсинга HTML
	q.Add("disable_notification", "false")
	q.Add("allow_sending_without_reply", "true")
	req.URL.RawQuery = q.Encode()

	// Отправляем запрос
	resp, err := client.Do(req)
	if err != nil {
		return fmt.Errorf("error sending telegram message: %v", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return fmt.Errorf("telegram API returned non-200 status code: %d", resp.StatusCode)
	}

	return nil
}

// SendTelegramMessage отправляет обычное сообщение через Telegram бот
func SendTelegramMessage(chatID string, message string) error {
	botToken := "7690904808:AAEyzgbEZ3--sQ1pkJ-bFBpnDSCY2rNq9VY"
	url := fmt.Sprintf("https://api.telegram.org/bot%s/sendMessage", botToken)

	// Создаем HTTP клиент
	client := &http.Client{}

	// Формируем запрос
	req, err := http.NewRequest("POST", url, nil)
	if err != nil {
		return fmt.Errorf("error creating request: %v", err)
	}

	// Добавляем параметры
	q := req.URL.Query()
	q.Add("chat_id", chatID)
	q.Add("text", message)
	req.URL.RawQuery = q.Encode()

	// Отправляем запрос
	resp, err := client.Do(req)
	if err != nil {
		return fmt.Errorf("error sending telegram message: %v", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return fmt.Errorf("telegram API returned non-200 status code: %d", resp.StatusCode)
	}

	return nil
}
