class NotificationManager {
    constructor() {
        this.ws = null;
        this.badge = document.querySelector('#notificationCount');
        this.button = document.querySelector('#notificationsBtn');
        this.modal = document.querySelector('.notifications-modal');
        this.unreadCount = 0;
        this.isTelegramWebView = this.checkIfTelegramWebView();
        this.init();
    }

    init() {
        this.connectWebSocket();
        this.addEventListeners();
        this.loadNotifications();
    }

    checkIfTelegramWebView() {
        return window.Telegram && window.Telegram.WebApp;
    }

    async loadNotifications() {
        const userId = localStorage.getItem('userId');
        if (!userId) return;

        try {
            const response = await fetch(`https://bgweb.nurali.uz/api/notifications/${userId}`);
            if (!response.ok) {
                throw new Error('Failed to fetch notifications');
            }
            const notifications = await response.json();
            
            // Проверяем, что notifications это массив
            if (Array.isArray(notifications)) {
                this.unreadCount = notifications.filter(n => !n.is_read).length;
                this.updateBadge();
                this.renderNotifications(notifications);
            } else {
                console.error('Notifications is not an array:', notifications);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    updateBadge() {
        if (this.badge) {
            this.badge.textContent = this.unreadCount;
            this.badge.style.display = this.unreadCount > 0 ? 'flex' : 'none';
        }
    }

    addEventListeners() {
        if (this.button) {
            this.button.addEventListener('click', () => {
                if (this.modal) {
                    this.modal.classList.toggle('active');
                    if (this.modal.classList.contains('active')) {
                        this.loadNotifications();
                    }
                }
            });
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.notifications-modal') && 
                !e.target.closest('#notificationsBtn')) {
                this.modal?.classList.remove('active');
            }
        });
    }

    connectWebSocket() {
        const userId = localStorage.getItem('userId');
        if (!userId) return;

        // Для мобильного Telegram используем long polling вместо WebSocket
        if (this.isTelegramWebView) {
            this.startPolling();
            return;
        }

        // Существующий WebSocket код для десктопа
        try {
            this.ws = new WebSocket(`wss://bgweb.nurali.uz/api/ws/${userId}`);
            
            this.ws.onopen = () => {
                console.log('WebSocket соединение установлено');
            };

            this.ws.onmessage = (event) => {
                const data = JSON.parse(event.data);
                this.handleNewNotification(data);
            };

            this.ws.onerror = () => {
                console.log('WebSocket ошибка, переключение на polling');
                this.startPolling();
            };

            this.ws.onclose = () => {
                if (!this.isTelegramWebView) {
                    setTimeout(() => this.connectWebSocket(), 5000);
                }
            };
        } catch (error) {
            console.error('Ошибка WebSocket:', error);
            this.startPolling();
        }
    }

    startPolling() {
        // Запускаем long polling каждые 10 секунд
        this.pollingInterval = setInterval(() => {
            this.pollNotifications();
        }, 10000);

        // Сразу делаем первый запрос
        this.pollNotifications();
    }

    async pollNotifications() {
        const userId = localStorage.getItem('userId');
        if (!userId) return;

        try {
            const response = await fetch(`https://bgweb.nurali.uz/api/notifications/${userId}/poll`);
            if (!response.ok) throw new Error('Ошибка получения уведомлений');
            
            const notifications = await response.json();
            if (Array.isArray(notifications) && notifications.length > 0) {
                notifications.forEach(notification => {
                    this.handleNewNotification(notification);
                });
            }
        } catch (error) {
            console.error('Ошибка при polling уведомлений:', error);
        }
    }

    handleNewNotification(notification) {
        this.unreadCount++;
        this.updateBadge();
        this.addNotificationToList(notification);

        // Специальная обработка для Telegram WebView
        if (this.isTelegramWebView && window.Telegram.WebApp) {
            window.Telegram.WebApp.HapticFeedback.notificationOccurred('success');
            // Используем нативные уведомления Telegram
            window.Telegram.WebApp.showPopup({
                title: 'Новое уведомление',
                message: 'У вас новое соглашение!',
                buttons: [{
                    type: 'ok'
                }]
            });
        } else {
            Utils.showNotification('У вас новое соглашение!', 'info');
        }
    }

    renderNotifications(notifications) {
        if (!this.modal || !Array.isArray(notifications)) return;

        const content = this.modal.querySelector('.notifications-content');
        if (!content) return;

        // Фильтруем только непрочитанные уведомления для модального окна
        const unreadNotifications = notifications.filter(n => !n.is_read);

        content.innerHTML = `
            <div class="notifications-header">
                <h5>Новые уведомления (${unreadNotifications.length})</h5>
                <a href="notifications.php" class="view-all-btn">
                    <i class="fas fa-history"></i>
                    История
                </a>
            </div>
            <div class="notifications-list">
                ${unreadNotifications.map(notification => this.renderNotificationItem(notification)).join('')}
            </div>
            ${unreadNotifications.length === 0 ? `
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <p>Нет новых уведомлений</p>
                </div>
            ` : ''}
        `;

        content.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => this.markAsRead(item.dataset.id));
        });
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`https://bgweb.nurali.uz/api/notifications/${notificationId}/read`, {
                method: 'PUT'
            });

            if (response.ok) {
                const item = this.modal?.querySelector(`[data-id="${notificationId}"]`);
                if (item?.classList.contains('unread')) {
                    item.classList.remove('unread');
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    this.updateBadge();
                }
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    // Метод для страницы уведомлений
    async loadAllNotifications() {
        const userId = localStorage.getItem('userId');
        if (!userId) return;

        try {
            const response = await fetch(`https://bgweb.nurali.uz/api/notifications/${userId}`);
            if (!response.ok) throw new Error('Failed to fetch notifications');
            
            const notifications = await response.json();
            if (!Array.isArray(notifications)) return;

            const container = document.querySelector('.notifications-list');
            if (!container) return;

            container.innerHTML = notifications
                .map(notification => this.renderNotificationItem(notification))
                .join('');
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    renderNotificationItem(notification) {
            const fromUser = notification.from_user || {};
            const adDetails = notification.ad_details || {};

            return `
                <div class="notification-item ${notification.is_read ? '' : 'unread'}" 
                     data-id="${notification.id}">
                    <div class="notification-header">
                        <div class="notification-user">
                            <i class="fas fa-user-circle"></i>
                            <span>${fromUser.name || 'Пользователь'}</span>
                        </div>
                        <div class="notification-time">
                            ${new Date(notification.created_at).toLocaleString()}
                        </div>
                    </div>
                    <div class="notification-content">

                        <div class="notification-ad">
                            <div class="ad-preview">
                                <div class="ad-info">
                                    <div class="ad-title">${adDetails.nickname || adDetails.name}</div>
                                    <div class="ad-id">ID публикации: ${notification.id}</div>
                                </div>
                            </div>
                            <div class="contact-info">
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>${fromUser.email}</span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <span>${fromUser.phone}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
    }
}

// Инициализация в зависимости от страницы
document.addEventListener('DOMContentLoaded', () => {
    const notificationManager = new NotificationManager();
    
    // Если мы на странице уведомлений, загружаем все уведомления
    if (window.location.pathname.includes('notifications.php')) {
        notificationManager.loadAllNotifications();
    }
});

// Добавим стили в header.php 