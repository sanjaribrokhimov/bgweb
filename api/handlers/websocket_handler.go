package handlers

import (
    "github.com/gin-gonic/gin"
    "github.com/gorilla/websocket"
    "log"
    "net/http"
    "strconv"
    "sync"
)

var upgrader = websocket.Upgrader{
    CheckOrigin: func(r *http.Request) bool {
        return true // Разрешаем все подключения (в продакшене нужно настроить проверку)
    },
    ReadBufferSize:  1024,
    WriteBufferSize: 1024,
}

// Структура для хранения соединений
type WebSocketHub struct {
    clients    map[uint]map[*websocket.Conn]bool
    mutex      sync.RWMutex
}

var Hub = &WebSocketHub{
    clients: make(map[uint]map[*websocket.Conn]bool),
}

// Обработчик WebSocket соединений
func HandleWebSocket(c *gin.Context) {
    log.Printf("New WebSocket connection attempt from user: %s", c.Param("user_id"))
    
    userId := c.Param("user_id")
    if userId == "" {
        c.JSON(400, gin.H{"error": "User ID is required"})
        return
    }

    userIdInt, err := strconv.ParseUint(userId, 10, 32)
    if err != nil {
        c.JSON(400, gin.H{"error": "Invalid user ID"})
        return
    }

    // Обновляем соединение до WebSocket
    conn, err := upgrader.Upgrade(c.Writer, c.Request, nil)
    if err != nil {
        log.Printf("Failed to upgrade connection: %v", err)
        return
    }

    log.Printf("WebSocket connection established for user: %d", userIdInt)

    // Регистрируем соединение
    Hub.mutex.Lock()
    if Hub.clients[uint(userIdInt)] == nil {
        Hub.clients[uint(userIdInt)] = make(map[*websocket.Conn]bool)
    }
    Hub.clients[uint(userIdInt)][conn] = true
    Hub.mutex.Unlock()

    // Удаляем соединение при закрытии
    defer func() {
        log.Printf("Closing WebSocket connection for user: %d", userIdInt)
        Hub.mutex.Lock()
        delete(Hub.clients[uint(userIdInt)], conn)
        if len(Hub.clients[uint(userIdInt)]) == 0 {
            delete(Hub.clients, uint(userIdInt))
        }
        Hub.mutex.Unlock()
        conn.Close()
    }()

    // Держим соединение открытым и читаем сообщения
    for {
        _, _, err := conn.ReadMessage()
        if err != nil {
            if websocket.IsUnexpectedCloseError(err, websocket.CloseGoingAway, websocket.CloseAbnormalClosure) {
                log.Printf("WebSocket error: %v", err)
            }
            break
        }
    }
}

// Отправка уведомления через WebSocket
func SendWebSocketNotification(userId uint, notification interface{}) {
    Hub.mutex.RLock()
    connections := Hub.clients[userId]
    Hub.mutex.RUnlock()

    if connections != nil {
        for conn := range connections {
            err := conn.WriteJSON(notification)
            if err != nil {
                log.Printf("Failed to send notification: %v", err)
            }
        }
    }
} 