package utils

import (
	"fmt"
	"net/smtp"
	"os"
)

func SendOTP(to string, otp string) error {
	from := os.Getenv("SMTP_EMAIL")
	password := os.Getenv("SMTP_PASSWORD")
	smtpHost := os.Getenv("SMTP_HOST")
	smtpPort := os.Getenv("SMTP_PORT")

	// Создаем сообщение
	headers := make(map[string]string)
	headers["From"] = from
	headers["To"] = to
	headers["Subject"] = "=?UTF-8?B?0JrQvtC0INC/0L7QtNGC0LLQtdGA0LbQtNC10L3QuNGP?="
	headers["MIME-Version"] = "1.0"
	headers["Content-Type"] = "text/plain; charset=UTF-8"

	message := ""
	for key, value := range headers {
		message += fmt.Sprintf("%s: %s\r\n", key, value)
	}
	message += fmt.Sprintf("\r\nВаш код подтверждения: %s\r\n", otp)

	// Создаем аутентификацию
	auth := smtp.PlainAuth("", from, password, smtpHost)

	// Отправляем email
	err := smtp.SendMail(smtpHost+":"+smtpPort, auth, from, []string{to}, []byte(message))
	if err != nil {
		return fmt.Errorf("ошибка отправки email: %v", err)
	}

	return nil
}

func SendEmail(to, subject, body string) error {
	// Получаем данные для SMTP из переменных окружения
	from := os.Getenv("SMTP_EMAIL")
	password := os.Getenv("SMTP_PASSWORD")
	smtpHost := os.Getenv("SMTP_HOST")
	smtpPort := os.Getenv("SMTP_PORT")

	// Формируем заголовки письма
	headers := make(map[string]string)
	headers["From"] = from
	headers["To"] = to
	headers["Subject"] = subject
	headers["MIME-Version"] = "1.0"
	headers["Content-Type"] = "text/plain; charset=UTF-8"

	// Собираем сообщение
	message := ""
	for key, value := range headers {
		message += fmt.Sprintf("%s: %s\r\n", key, value)
	}
	message += "\r\n" + body

	// Создаем аутентификацию
	auth := smtp.PlainAuth("", from, password, smtpHost)

	// Отправляем email
	err := smtp.SendMail(
		smtpHost+":"+smtpPort,
		auth,
		from,
		[]string{to},
		[]byte(message),
	)

	if err != nil {
		return fmt.Errorf("failed to send email: %v", err)
	}

	return nil
}
