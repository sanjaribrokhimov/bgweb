package utils

import (
    "fmt"
    "os"
    "time"
    "path/filepath"
)

type Logger struct {
    LogFile string
}

func NewLogger() *Logger {
    // Создаем папку logs если её нет
    if err := os.MkdirAll("logs", 0755); err != nil {
        fmt.Printf("Error creating logs directory: %v\n", err)
    }

    // Формируем имя файла на основе текущей недели года
    _, week := time.Now().ISOWeek()
    filename := filepath.Join("logs", fmt.Sprintf("errors_week_%d.txt", week))

    return &Logger{
        LogFile: filename,
    }
}

func (l *Logger) LogError(source string, err error, details string) {
    // Формируем сообщение об ошибке
    timestamp := time.Now().Format("2006-01-02 15:04:05")
    logMessage := fmt.Sprintf("[%s] Source: %s\nError: %v\nDetails: %s\n\n",
        timestamp, source, err, details)

    // Открываем файл для добавления записи
    file, err := os.OpenFile(l.LogFile, os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0644)
    if err != nil {
        fmt.Printf("Error opening log file: %v\n", err)
        return
    }
    defer file.Close()

    // Записываем сообщение
    if _, err := file.WriteString(logMessage); err != nil {
        fmt.Printf("Error writing to log file: %v\n", err)
    }
}

// Очистка старых логов (старше 2 недель)
func (l *Logger) CleanOldLogs() {
    files, err := filepath.Glob("logs/errors_week_*.txt")
    if err != nil {
        fmt.Printf("Error finding log files: %v\n", err)
        return
    }

    currentTime := time.Now()
    for _, file := range files {
        info, err := os.Stat(file)
        if err != nil {
            continue
        }

        // Удаляем файлы старше 2 недель
        if currentTime.Sub(info.ModTime()) > 14*24*time.Hour {
            os.Remove(file)
        }
    }
} 