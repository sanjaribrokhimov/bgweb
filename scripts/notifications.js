class NotificationManager {
    constructor() {
        this.badge = document.querySelector('#notificationCount');
        this.button = document.querySelector('#notificationsBtn');
        this.modal = document.querySelector('.notifications-modal');
        this.unreadCount = 0;
        this.pollingInterval = null;
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
            const response = await fetch(`http://localhost:8888/api/notifications/${userId}`);
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
        const container = document.querySelector('.notifications-list');
        if (!container) return;

        if (!notifications.length) {
            container.innerHTML = '<div class="empty-state">Нет уведомлений</div>';
            return;
        }

        container.innerHTML = notifications.map(notification => `
            <div class="notification-card ${!notification.is_read ? 'unread' : ''}" 
                 data-id="${notification.id}"
                 onclick="notificationManager.openModal(${JSON.stringify(notification).replace(/"/g, '&quot;')})">
                <div class="notification-header">
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span>${notification.from_user.name}</span>
                    </div>
                    <div class="time">${new Date(notification.created_at).toLocaleString()}</div>
                </div>
                <div class="notification-ad-info">
                    <p><i class="fas fa-bullhorn"></i> ${notification.ad_details?.nickname || notification.ad_details?.name || 'Название не указано'}</p>
                </div>
                <div class="notification-body">
                    ${notification.message}
                </div>
            </div>
        `).join('');
    }

    async openModal(notification) {
        if (!notification.is_read) {
            try {
                await this.markAsRead(notification.id);
                const card = document.querySelector(`.notification-card[data-id="${notification.id}"]`);
                if (card) {
                    card.classList.remove('unread');
                }
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                this.updateBadge();
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }

        const modal = document.getElementById('notificationModal');
        const adDetails = notification.ad_details;
        const fromUser = notification.from_user;
        
        modal.querySelector('.modal-body').innerHTML = `
            <div class="modal-sections">
                <!-- Секция с объявлением -->
                <div class="ad-section">
                    <div class="ad-header">
                        <h6>Объявление</h6>
                        <span class="ad-type ${notification.ad_type}">
                            ${this.getAdTypeLabel(notification.ad_type)}
                        </span>
                    </div>
                    <div class="ad-content">
                        <div class="ad-image">
                            <img src="${adDetails.photo_base64}" alt="Фото объявления" 
                                 onerror="this.src='./img/noImage.jpg'">
                        </div>
                        <div class="ad-info">
                            <h5>${adDetails.nickname || adDetails.name}</h5>
                            <p class="ad-comment">${adDetails.ad_comment}</p>
                        </div>
                    </div>
                </div>

                <!-- Секция с отправителем -->
                <div class="sender-section">
                    <div class="sender-header">
                        <h6>Информация об отправителе</h6>
                        <span class="date">${new Date(notification.created_at).toLocaleString()}</span>
                    </div>
                    <div class="sender-info">
                        <div class="contact-item">
                            <i class="fas fa-user"></i>
                            <span>Имя: ${fromUser.name}</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-tag"></i>
                            <span>Категория: ${fromUser.category || 'Не указана'}</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-compass"></i>
                            <span>Направление: ${fromUser.direction || 'Не указано'}</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>Телефон: ${fromUser.phone}</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>Email: ${fromUser.email}</span>
                        </div>
                        <div class="contact-item">
                            <i class="fab fa-telegram"></i>
                            <span>Telegram: <a href="https://t.me/${fromUser.telegram}" target="_blank" class="telegram-link">@${fromUser.telegram}</a></span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        new bootstrap.Modal(modal).show();
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

    async markAsRead(notificationId) {
        const response = await fetch(`http://localhost:8888/api/notifications/${notificationId}/read`, {
            method: 'PUT'
        });
        
        if (!response.ok) {
            throw new Error('Failed to mark notification as read');
        }
    }
}

// Инициализация
document.addEventListener('DOMContentLoaded', () => {
    window.notificationManager = new NotificationManager();
});