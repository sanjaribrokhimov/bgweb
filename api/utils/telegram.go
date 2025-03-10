package utils

import (
	"fmt"
	"net/http"
)

// SendTelegramOTP отправляет OTP код через Telegram бот с высоким приоритетом
func SendTelegramOTP(chatID string, otp string) error {
	botToken := "7690904808:AAEyzgbEZ3--sQ1pkJ-bFBpnDSCY2rNq9VY"

	// Сначала отправляем пустое сообщение с уведомлением
	notificationURL := fmt.Sprintf("https://api.telegram.org/bot%s/sendMessage", botToken)
	client := &http.Client{}
	notifReq, err := http.NewRequest("POST", notificationURL, nil)
	if err != nil {
		return fmt.Errorf("error creating notification request: %v", err)
	}

	q := notifReq.URL.Query()
	q.Add("chat_id", chatID)
	q.Add("text", "⚠️ Новый код подтверждения")
	notifReq.URL.RawQuery = q.Encode()

	_, err = client.Do(notifReq)
	if err != nil {
		return fmt.Errorf("error sending notification: %v", err)
	}

	// Затем отправляем сам код
	message := fmt.Sprintf("<b>🔔 Ваш код подтверждения:</b> <code>%s</code>", otp)
	url := fmt.Sprintf("https://api.telegram.org/bot%s/sendMessage", botToken)

	// Создаем HTTP клиент
	client = &http.Client{}

	// Формируем запрос
	req, err := http.NewRequest("POST", url, nil)
	if err != nil {
		return fmt.Errorf("error creating request: %v", err)
	}

	// Добавляем параметры
	q = req.URL.Query()
	q.Add("chat_id", chatID)
	q.Add("text", message)
	q.Add("parse_mode", "HTML")
	q.Add("disable_notification", "false")
	q.Add("allow_sending_without_reply", "true")
	q.Add("disable_web_page_preview", "true") // Отключаем предпросмотр ссылок
	q.Add("protect_content", "false")         // Убираем защиту контента
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
