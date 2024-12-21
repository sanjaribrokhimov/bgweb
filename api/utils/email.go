package utils

import (
	"fmt"
	"log"
	"net/smtp"
	"os"
)

func SendOTP(email string, otp string) error {
	// Конфигурация SMTP
	from := os.Getenv("SMTP_EMAIL")
	password := os.Getenv("SMTP_PASSWORD")
	smtpHost := os.Getenv("SMTP_HOST")
	smtpPort := os.Getenv("SMTP_PORT")

	// Создаем сообщение
	message := []byte(fmt.Sprintf("Subject: Код подтверждения\r\n\r\nВаш код подтверждения: %s", otp))

	// Настройка аутентификации
	auth := smtp.PlainAuth("", from, password, smtpHost)

	// Отправляем email
	err := smtp.SendMail(smtpHost+":"+smtpPort, auth, from, []string{email}, message)
	if err != nil {
		log.Printf("Ошибка отправки email: %v", err)
		return err
	}

	return nil
}

func SendEmail(to, subject, body string) error {
	// Получаем данные для SMTP из переменных окружения
	from := os.Getenv("SMTP_EMAIL")
	password := os.Getenv("SMTP_PASSWORD")
	smtpHost := os.Getenv("SMTP_HOST")
	smtpPort := os.Getenv("SMTP_PORT")

	// Логируем параметры для отладки
	log.Printf("Sending email to: %s", to)
	log.Printf("SMTP settings - Host: %s, Port: %s", smtpHost, smtpPort)

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

	// Отправляем email с логированием
	err := smtp.SendMail(
		smtpHost+":"+smtpPort,
		auth,
		from,
		[]string{to},
		[]byte(message),
	)

	if err != nil {
		log.Printf("Error sending email: %v", err)
		return fmt.Errorf("failed to send email: %v", err)
	}

	log.Printf("Email sent successfully to: %s", to)
	return nil
}
