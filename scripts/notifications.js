class NotificationManager {
    constructor() {
        this.badge = document.querySelector('#notificationCount');
        this.button = document.querySelector('#notificationsBtn');
        this.container = document.querySelector('.notifications-list');
        this.unreadCount = 0;
        this.init();
    }

    init() {
        this.startPolling();
        this.addEventListeners();
        this.loadNotifications();
    }

    startPolling() {
        if (this.pollingInterval) return;
        
        // Проверяем новые уведомления каждые 10 секунд
        this.pollingInterval = setInterval(() => {
            this.loadNotifications();
        }, 20000);
    }

    addEventListeners() {
        if (this.button) {
            this.button.addEventListener('click', () => {
                // Перенаправляем на страницу уведомлений
                window.location.href = 'notifications.php';
            });
        }
    }

    updateBadge() {
        if (this.badge) {
            this.badge.textContent = this.unreadCount;
            this.badge.style.display = this.unreadCount > 0 ? 'flex' : 'none';
        }
    }

    async loadNotifications() {
        const userId = localStorage.getItem('userId');
        if (!userId) return;

        try {
            const response = await fetch(`https://blogy.uz/api/notifications/${userId}`);
            const notifications = await response.json();
            
            // Обновляем количество непрочитанных
            this.unreadCount = notifications.filter(n => !n.is_read).length;
            this.updateBadge();
            
            this.renderNotifications(notifications);
        } catch (error) {
            console.error('Error:', error);
            this.showError();
        }
    }

    renderNotifications(notifications) {
        if (!this.container) return;

        if (!notifications.length) {
            this.container.innerHTML = '<div class="empty-state">Нет уведомлений</div>';
            return;
        }

        this.container.innerHTML = notifications.map(notification => `
            <div class="notification-card ${!notification.is_read ? 'unread' : ''}" data-id="${notification.id}">
                <div class="notification-main" onclick="notificationManager.toggleNotification(this.closest('.notification-card'), ${JSON.stringify(notification).replace(/"/g, '&quot;')})">
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span class="${!notification.is_read ? 'heavy-blur' : ''}">${notification.from_user.name}</span>
                        <span class="date">${new Date(notification.created_at).toLocaleString()}</span>
                    </div>

                    <div class="ad-content">
                        <div class="ad-image">
                            <img src="${notification.ad_details.photo_base64}" 
                                 alt="Фото объявления" 
                                 onerror="this.src='./img/noImage.jpg'">
                        </div>
                        <div class="ad-info">
                            <h3 class="${!notification.is_read ? 'heavy-blur' : ''}">${notification.ad_details.nickname || notification.ad_details.name}</h3>
                            <span class="ad-type ${notification.ad_type} ${!notification.is_read ? 'heavy-blur' : ''}">
                                ${this.getAdTypeLabel(notification.ad_type)}
                            </span>
                        </div>
                    </div>

                    <div class="details-content">
                        <div class="info-item ${!notification.is_read ? 'heavy-blur' : ''}">
                            <i class="fas fa-tag"></i>
                            <span>Категория: ${notification.from_user.category || 'Не указана'}</span>
                        </div>
                        <div class="info-item ${!notification.is_read ? 'heavy-blur' : ''}">
                            <i class="fas fa-compass"></i>
                            <span>Направление: ${notification.from_user.direction || 'Не указано'}</span>
                        </div>
                        <div class="social-links ${!notification.is_read ? 'heavy-blur' : ''}">
                            ${notification.from_user.telegram ? 
                                `<a href="tg://resolve?domain=${notification.from_user.telegram.replace('@', '').replace('https://t.me/', '')}" target="_blank">
                                    <i class="fab fa-telegram"></i> Telegram
                                </a>` : ''
                            }
                            ${notification.from_user.instagram ? 
                                `<a href="${notification.from_user.instagram}" target="_blank">
                                    <i class="fab fa-instagram"></i> Instagram
                                </a>` : ''
                            }
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    async toggleNotification(card, notification) {
        if (!notification.is_read) {
            try {
                await this.markAsRead(notification.id);
                card.classList.remove('unread');
                card.querySelectorAll('.blurred').forEach(el => el.classList.remove('blurred'));
                notification.is_read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                this.updateBadge();
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }
    }

    async markAsRead(notificationId) {
        const userId = localStorage.getItem('userId');
        if (!userId) return;

        try {
            const response = await fetch(`https://blogy.uz/api/notifications/${notificationId}/read`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to mark notification as read');
            }

            return await response.json();
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }

    renderAdLinks(adDetails) {
        const links = [];
        
        if (adDetails.telegram_link) {
            links.push(`
                <a href="${adDetails.telegram_link}" target="_blank" class="social-link telegram">
                    <i class="fab fa-telegram"></i> Telegram
                </a>
            `);
        }
        
        if (adDetails.instagram_link) {
            links.push(`
                <a href="${adDetails.instagram_link}" target="_blank" class="social-link instagram">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
            `);
        }

        if (adDetails.youtube_link) {
            links.push(`
                <a href="${adDetails.youtube_link}" target="_blank" class="social-link youtube">
                    <i class="fab fa-youtube"></i> YouTube
                </a>
            `);
        }

        return links.join('') || '<span class="no-links">Ссылки не указаны</span>';
    }

    getAdTypeLabel(type) {
        return {
            'blogger': 'Блогер',
            'company': 'Компания',
            'freelancer': 'Фрилансер'
        }[type] || type;
    }

    getAdLink(notification) {
        const baseUrl = 'index.php';
        const type = notification.ad_type;
        const id = notification.ad_details?.ID || notification.ad_details?.id || notification.ad_id;
        
        if (!id) {
            console.error('Ad ID not found:', notification);
            return '#';
        }

        switch(type) {
            case 'blogger':
                return `${baseUrl}?page=bloggers&view=${id}`;
            case 'company':
                return `${baseUrl}?page=advertisers&view=${id}`;
            case 'freelancer':
                return `${baseUrl}?page=freelancers&view=${id}`;
            default:
                return '#';
        }
    }

    showError() {
        const container = document.querySelector('.notifications-list');
        if (container) {
            container.innerHTML = '<div class="error-state">Ошибка загрузки уведомлений</div>';
        }
    }

    showNotification(notification) {
        Utils.showNotification('У вас новое соглашение!', 'info');
    }
}

// Инициализация
document.addEventListener('DOMContentLoaded', () => {
    window.notificationManager = new NotificationManager();
});