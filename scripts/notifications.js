class NotificationManager {
    constructor() {
        this.badge = document.querySelector('#notificationCount');
        this.button = document.querySelector('#notificationsBtn');
        this.container = document.querySelector('.notifications-list');
        this.unreadCount = 0;
        this.init();
    }

    init() {
        // Добавляем стили для состояний
        const style = document.createElement('style');
        style.textContent = `
            .error-state, .empty-state {
                text-align: center;
                padding: 20px;
                color: var(--text-color);
            }

            .error-state i, .empty-state i {
                font-size: 24px;
                margin-bottom: 10px;
                color: var(--text-secondary);
            }

            .error-state p, .empty-state p {
                margin: 10px 0;
                color: var(--text-secondary);
            }

            .retry-btn {
                background: var(--accent-blue);
                border: none;
                padding: 8px 16px;
                border-radius: 8px;
                color: white;
                cursor: pointer;    
                transition: all 0.3s ease;
                margin-top: 10px;
            }

            .retry-btn:hover {
                opacity: 0.9;
                transform: translateY(-1px);
            }

            .retry-btn i {
                margin-right: 5px;
            }

            .message-section {
                margin-top: 10px;
                padding: 10px;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 8px;
            }

            .message-label {
                color: var(--text-secondary);
                font-size: 14px;
                margin-bottom: 5px;
            }

            .user-message {
                color: var(--text-color);
                margin: 0;
                font-size: 20px;
                line-height: 1.4;
            }

            .notification-card {
                background: var(--card-bg);
                border-radius: 12px;
                padding: 15px;
                margin-bottom: 10px;
                transition: all 0.3s ease;
            }

            .notification-card:hover {
                background: var(--card-hover);
            }

            .notification-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }

            .notification-header h3 {
                margin: 0;
                font-size: 16px;
                color: var(--text-color);
            }

            .date {
                font-size: 12px;
                color: var(--text-secondary);
            }
        `;
        document.head.appendChild(style);

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
        if (!userId) {
            // console.log('No userId found');
            return;
        }


        try {
            // Добавим логирование для отладки
            // console.log('Fetching notifications for user:', userId);
            

            const response = await fetch(`https://blogy.uz/api/notifications/${userId}`);
            
            // Проверяем статус ответа
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const notifications = await response.json();
            // console.log('Received notifications:', notifications);
            

            // Проверяем, что notifications это массив
            if (!Array.isArray(notifications)) {
                throw new Error('Received invalid notifications data');
            }
            
            // Обновляем количество непрочитанных
            this.unreadCount = notifications.filter(n => !n.is_read).length;
            this.updateBadge();
            
            // Проверяем наличие контейнера перед рендерингом
            if (this.container) {
                this.renderNotifications(notifications);
            } else {
                // console.log('Notifications container not found');
            }

        } catch (error) {
            console.error('Error loading notifications:', error);
            
            // Более информативное сообщение об ошибке
            if (this.container) {
                this.container.innerHTML = `
                    <div class="error-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Не удалось загрузить уведомления</p>
                        <button onclick="notificationManager.loadNotifications()" class="retry-btn">
                            <i class="fas fa-redo"></i> Повторить
                        </button>
                    </div>
                `;
            }
        }
    }

    renderNotifications(notifications) {
        if (!this.container) {
            console.log('Container not found');
            return;
        }

        if (!notifications || notifications.length === 0) {
            this.container.innerHTML = `
                <div class="empty-state">
                    <i class="far fa-bell-slash"></i>
                    <p>У вас пока нет уведомлений</p>
                </div>
            `;
            return;
        }

        this.container.innerHTML = notifications.map(notification => 
            this.createNotificationCard(notification)
        ).join('');
    }

    createNotificationCard(notification) {
        const isUnread = !notification.is_read;
        const blurClass = isUnread ? 'heavy-blur' : '';
        
        return `
            <div class="notification-card ${isUnread ? 'unread' : ''}" data-id="${notification.id}">
                <div class="notification-main" onclick="notificationManager.toggleNotification(this.closest('.notification-card'), ${JSON.stringify(notification).replace(/"/g, '&quot;')})">
                    <div class="notification-header">
                        <h3>У вас новое соглашение!</h3>
                        <span class="date">${new Date(notification.created_at).toLocaleString()}</span>
                    </div>

                    <div class="sender-info">
                        <div class="user-details">
                            от
                            <i class="fas fa-user-circle"></i>
                            <span class="username ${blurClass}">${notification.from_user.name}</span>
                        </div>
                    </div>
                    
                    ${notification.user_message ? `
                        <div class="message-section">
                            <p class="message-label">Сообщение от пользователя:</p> 
                            <p class="user-message ${blurClass}">${notification.user_message}</p>
                        </div>
                    ` : ''}

                    ${notification.ad_details ? this.createAdPreview(notification.ad_details, blurClass) : ''}
                    ${this.createDetailsSection(notification.from_user, blurClass)}
                                        
                </div>
            </div>
        `;
    }

    createAdPreview(adDetails, blurClass) {
        if (!adDetails) return '';
        
        return `
            <div class="ad-preview">
                <div class="ad-image">
                    <img src="${adDetails.photo_base64 || './img/noImage.jpg'}" 
                         alt="Фото объявления" 
                         onerror="this.src='./img/noImage.jpg'">
                </div>
                
                <div class="ad-info">
                    <p style="font-size: 12px;"> на объявление</p>
                    <h4 class="${blurClass}">${adDetails.nickname || adDetails.name || 'Без названия'}</h4>
                </div>
            </div>
        `;
    }

    createDetailsSection(userDetails, blurClass) {
        if (!userDetails) return '';
        
        return `
            <p style="color:green;"> Данные пользователя</p>
            <div class="details-section">
                <div class="category-direction ${blurClass}">
                    <div class="info-row">
                        <i class="fas fa-tag"></i>
                        <span>Категория: ${userDetails.category || 'Не указана'}</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-compass"></i>
                        <span>Направление: ${userDetails.direction || 'Не указано'}</span>
                    </div>
                </div>
                
                ${this.createSocialLinks(userDetails, blurClass)}
            </div>
        `;
    }

    createSocialLinks(userDetails, blurClass) {
        const links = [];
        
        if (userDetails.telegram) {
            const telegramUsername = userDetails.telegram.replace('@', '').replace('https://t.me/', '');
            links.push(`
                <a href="https://t.me/${telegramUsername}" class="social-link telegram" target="_blank">
                    <i class="fab fa-telegram"></i> Telegram
                </a>
                <br>
            `);
        }
        
        if (userDetails.instagram) {
            links.push(`
                <a href="${userDetails.instagram}" class="social-link instagram">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
            `);
        }

        return links.length ? `
            <div class="social-links ${blurClass}">
                ${links.join('')}
            </div>
        ` : '';
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
            container.innerHTML = `
                <div class="error-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Не удалось загрузить уведомления</p>
                    <button onclick="notificationManager.loadNotifications()" class="retry-btn">
                        <i class="fas fa-redo"></i> Повторить
                    </button>
                </div>
            `;
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