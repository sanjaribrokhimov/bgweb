<?php include 'components/miniHeader.php'; ?>

<!-- Добавляем переключатель языков -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключатель языка
    const langButtons = document.querySelectorAll('.lang-btn');
    const currentLang = localStorage.getItem('selectedLanguage') || 'ru';

    // Устанавливаем активную кнопку
    langButtons.forEach(btn => {
        if (btn.dataset.lang === currentLang) {
            btn.classList.add('active');
        }

        btn.addEventListener('click', () => {
            langButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const newLang = btn.dataset.lang;
            localStorage.setItem('selectedLanguage', newLang);
            
            // Создаем и диспатчим событие смены языка
            const event = new Event('languageChanged');
            document.dispatchEvent(event);
        });
    });
});
</script>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Custom styles -->
<link rel="stylesheet" href="styles.css">

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Utils script -->
<script src="scripts/utils.js"></script>

<div class="container mt-4">
    <!-- Результаты поиска -->
    <div class="search-results-container">
        <h3 class="mb-3">Результаты поиска</h3>
        <div class="products-grid">
            <!-- Карточки будут добавляться динамически -->
        </div>
    </div>
</div>

<!-- Модальное окно для подробной информации -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подробная информация</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Контент будет добавлен динамически -->
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения -->
<div class="confirm-modal">
    <div class="confirm-content">
        <button class="confirm-yes">
            <i class="fas fa-check"></i>
        </button>
        <button class="confirm-no">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Уведомления -->
<div class="notification-toast">
    <div class="notification-content">
        <i class="fas fa-check-circle"></i>
        <span>Уведомление отправлено</span>
    </div>
</div>

<script>
class SearchResultsLoader {
    constructor() {
        this.page = 1;
        this.limit = 10;
        this.loading = false;
        this.hasMore = true;
        this.query = new URLSearchParams(window.location.search).get('q');
        this.type = new URLSearchParams(window.location.search).get('type');
        this.selectedId = new URLSearchParams(window.location.search).get('selected');
        
        this.init();
    }

    async init() {
        if (!this.query || !this.type) {
            window.location.href = 'index.php';
            return;
        }

        await this.loadSearchResults();
        this.setupInfiniteScroll();
    }

    async loadSearchResults() {
        if (this.loading || !this.hasMore) return;
        
        this.loading = true;
        const grid = document.querySelector('.products-grid');
        const loadingIndicator = document.getElementById('loadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'block';
        
        try {
            const url = `https://173.212.234.202/api/ads/search?q=${encodeURIComponent(this.query)}&type=${this.type}&page=${this.page}&limit=${this.limit}`;
            const response = await fetch(url);
            if (!response.ok) throw new Error('Failed to fetch search results');
            
            const data = await response.json();
            const results = data.results[this.type] || [];

            if (results.length < this.limit) {
                this.hasMore = false;
            }

            results.forEach(item => {
                const card = this.createCard(item.data);
                grid.appendChild(card);
            });

            this.page++;
        } catch (error) {
            console.error('Error loading search results:', error);
        } finally {
            this.loading = false;
            if (loadingIndicator) loadingIndicator.style.display = 'none';
        }
    }

    createCard(data) {
        const card = document.createElement('div');
        card.className = 'product-card';
        
        // Проверяем и используем правильный ID
        const itemId = data.id || data.ID; // Поддерживаем оба варианта
        const userId = data.user_id || data.userId;
        
        let cardContent = '';
        
        switch(this.type) {
            case 'bloggers':
                cardContent = `
                    <div class="product-image">
                        <img src="${data.photo_base64}" alt="Блогер" onerror="this.src='./img/noImage.jpg'">
                        <div class="card-type-badge">Блогер</div>
                    </div>
                    <div class="product-info">
                        <h3 class="notranslate">${data.nickname}</h3>
                        <div class="category-tag">${data.category}</div>
                        <div class="stats">
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span><span class="notranslate">${this.formatNumber(data.followers)}</span> подписчиков</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-chart-line"></i>
                                <span>ER: <span class="notranslate">${data.engagement}%</span></span>
                            </div>
                        </div>
                        <div class="social-networks notranslate">
                            ${data.telegram_link ? `<a href="${data.telegram_link}">Telegram</a>` : ''}
                            ${data.instagram_link ? `<a href="${data.instagram_link}">Instagram</a>` : ''}
                        </div>
                    </div>
                `;
                break;

            case 'companies':
                cardContent = `
                    <div class="product-image">
                        <img src="${data.photo_base64}" alt="Компания" onerror="this.src='./img/noImage.jpg'">
                        <div class="card-type-badge">Компания</div>
                    </div>
                    <div class="product-info">
                        <h3>${data.name || 'Без названия'}</h3>
                        <div class="category-tag">${data.category || 'Без категории'}</div>
                        <div class="stats">
                            <div class="stat-item">
                                <i class="fas fa-briefcase"></i>
                                <span>Бюджет: ${data.budget ? this.formatNumber(data.budget) + '$' : 'Не указан'}</span>
                            </div>
                        </div>
                        <div class="btn-actions">
                            <button class="btn-details" data-id="${itemId}" data-type="company">
                                <i class="fas fa-info-circle"></i> Подробнее
                            </button>
                            <button class="btn-accept" data-id="${itemId}" data-owner-id="${userId}">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>
                `;
                break;

            case 'freelancers':
                cardContent = `
                    <div class="product-image">
                        <img src="${data.photo_base64 ? 
                            `data:image/jpeg;base64,${data.photo_base64}` : 
                            './img/noImage.jpg'}" alt="Фрилансер">
                        <div class="card-type-badge">Фрилансер</div>
                    </div>
                    <div class="product-info">
                        <h3>${data.name || 'Без имени'}</h3>
                        <div class="category-tag">${data.category || 'Без категории'}</div>
                        <div class="stats">
                            <div class="stat-item">
                                <i class="fas fa-laptop-code"></i>
                                <span>${data.category}</span>
                            </div>
                        </div>
                        <div class="btn-actions">
                            <button class="btn-details" data-id="${data.ID}" data-type="freelancer">
                                <i class="fas fa-info-circle"></i> Подробнее
                            </button>
                            <button class="btn-accept" data-id="${data.ID}" data-owner-id="${data.user_id}">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>
                `;
                break;
        }

        card.innerHTML = cardContent;

        // Добавляем обработчики событий
        const detailsBtn = card.querySelector('.btn-details');
        const acceptBtn = card.querySelector('.btn-accept');

        if (detailsBtn) {
            detailsBtn.addEventListener('click', () => {
                const id = detailsBtn.dataset.id;
                const type = detailsBtn.dataset.type;
                if (id && type) {
                    this.showDetails(id, type);
                }
            });
        }

        if (acceptBtn) {
            acceptBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.showConfirmModal(acceptBtn);
            });
        }

        return card;
    }

    async showDetails(id, type) {
        try {
            console.log('Fetching details for:', { id, type }); // Добавляем логирование
            
            if (!id || !type) {
                throw new Error('Missing id or type');
            }

            const response = await fetch(`https://173.212.234.202/api/ads/details/${type}/${id}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Received data:', data); // Добавляем логирование

            const modalContent = document.getElementById('detailsContent');
            modalContent.innerHTML = this.createDetailsContent(data, type);

            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            modal.show();
        } catch (error) {
            console.error('Error:', error);
            if (typeof Utils !== 'undefined') {
                Utils.showNotification('Ошибка при загрузке данных', 'error');
            } else {
                alert('Ошибка при загрузке данных');
            }
        }
    }

    createDetailsContent(data, type) {
        const photoUrl = data.photo_base64 || './img/noImage.jpg';
        
        switch(type) {
            case 'blogger':
                return `
                    <div class="details-card">
                        <div class="details-header">
                            <img src="${photoUrl}" 
                                 alt="Profile" 
                                 class="profile-image"
                                 onerror="this.src='./img/noImage.jpg'">
                            <div class="header-info">
                                <h4>${data.nickname || 'Без имени'}</h4>
                                <div class="details-stats">
                                    <span><i class="fas fa-users"></i> ${this.formatNumber(data.followers)} подписчиков</span>
                                    <span><i class="fas fa-chart-line"></i> ER: ${data.engagement || 0}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="details-info">
                            <div class="info-item">
                                <label><i class="fas fa-tag"></i> Категория</label>
                                <span>${data.category || 'Не указана'}</span>
                            </div>
                            <div class="social-networks">
                                ${data.telegram_link ? `
                                    <a href="${data.telegram_link}" target="_blank" class="social-link">
                                        <i class="fab fa-telegram"></i> Telegram
                                    </a>
                                ` : ''}
                                ${data.instagram_link ? `
                                    <a href="${data.instagram_link}" target="_blank" class="social-link">
                                        <i class="fab fa-instagram"></i> Instagram
                                    </a>
                                ` : ''}
                                ${data.youtube_link ? `
                                    <a href="${data.youtube_link}" target="_blank" class="social-link">
                                        <i class="fab fa-youtube"></i> YouTube
                                    </a>
                                ` : ''}
                            </div>
                            <div class="description">
                                <h5>Описание</h5>
                                <p>${data.ad_comment || 'Описание отсутствует'}</p>
                            </div>
                        </div>
                    </div>
                `;

            case 'company':
                return `
                    <div class="details-card">
                        <div class="details-header">
                            <img src="${photoUrl}" 
                                 alt="Company" 
                                 class="profile-image"
                                 onerror="this.src='./img/noImage.jpg'">
                            <div class="header-info">
                                <h4>${data.name || 'Без названия'}</h4>
                                <div class="details-stats">
                                    <span><i class="fas fa-briefcase"></i> Бюджет: ${this.formatNumber(data.budget)}$</span>
                                </div>
                            </div>
                        </div>
                        <div class="details-info">
                            <div class="info-item">
                                <label><i class="fas fa-tag"></i> Категория</label>
                                <span>${data.category || 'Не указана'}</span>
                            </div>
                            <div class="social-networks">
                                ${data.website_link ? `
                                    <a href="${data.website_link}" target="_blank" class="social-link">
                                        <i class="fas fa-globe"></i> Веб-сайт
                                    </a>
                                ` : ''}
                                ${data.telegram_link ? `
                                    <a href="${data.telegram_link}" target="_blank" class="social-link">
                                        <i class="fab fa-telegram"></i> Telegram
                                    </a>
                                ` : ''}
                                ${data.instagram_link ? `
                                    <a href="${data.instagram_link}" target="_blank" class="social-link">
                                        <i class="fab fa-instagram"></i> Instagram
                                    </a>
                                ` : ''}
                            </div>
                            <div class="description">
                                <h5>Описание</h5>
                                <p>${data.ad_comment || 'Описание отсутствует'}</p>
                            </div>
                        </div>
                    </div>
                `;

            case 'freelancer':
                return `
                    <div class="details-card">
                        <div class="details-header">
                            <img src="${photoUrl}" 
                                 alt="Freelancer" 
                                 class="profile-image"
                                 onerror="this.src='./img/noImage.jpg'">
                            <div class="header-info">
                                <h4>${data.name || 'Без имени'}</h4>
                            </div>
                        </div>
                        <div class="details-info">
                            <div class="info-item">
                                <label><i class="fas fa-tag"></i> Категория</label>
                                <span>${data.category || 'Не указана'}</span>
                            </div>
                            <div class="social-networks">
                                ${data.github_link ? `
                                    <a href="${data.github_link}" target="_blank" class="social-link">
                                        <i class="fab fa-github"></i> GitHub
                                    </a>
                                ` : ''}
                                ${data.portfolio_link ? `
                                    <a href="${data.portfolio_link}" target="_blank" class="social-link">
                                        <i class="fas fa-briefcase"></i> Портфолио
                                    </a>
                                ` : ''}
                                ${data.telegram_link ? `
                                    <a href="${data.telegram_link}" target="_blank" class="social-link">
                                        <i class="fab fa-telegram"></i> Telegram
                                    </a>
                                ` : ''}
                            </div>
                            <div class="description">
                                <h5>Описание</h5>
                                <p>${data.ad_comment || 'Описание отсутствует'}</p>
                            </div>
                        </div>
                    </div>
                `;
        }
    }

    formatNumber(num) {
        if (!num) return '0';
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    setupInfiniteScroll() {
        window.addEventListener('scroll', () => {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
                this.loadSearchResults();
            }
        });
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    new SearchResultsLoader();
});

// Добавляем обработчики для модального окна подтверждения
document.addEventListener('DOMContentLoaded', () => {
    const confirmModal = document.querySelector('.confirm-modal');
    const confirmYes = confirmModal?.querySelector('.confirm-yes');
    const confirmNo = confirmModal?.querySelector('.confirm-no');

    // Глобальная функция для показа модального окна подтверждения
    window.showConfirmModal = function(button) {
        const rect = button.getBoundingClientRect();
        confirmModal.style.top = `${rect.top}px`;
        confirmModal.style.left = `${rect.left}px`;
        confirmModal.classList.add('active');
        confirmModal.currentButton = button;
    };

    // Обработчик для кнопки "Да"
    if (confirmYes) {
        confirmYes.addEventListener('click', async () => {
            const button = confirmModal.currentButton;
            if (!button) return;

            try {
                const adId = button.dataset.id;
                const fromUserId = localStorage.getItem('userId');
                const toUserId = button.dataset.ownerId;
                const detailsBtn = button.closest('.btn-actions').querySelector('.btn-details');
                const adType = detailsBtn ? detailsBtn.dataset.type : 'blogger';

                const response = await fetch('https://173.212.234.202/api/notifications/accept', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        from_user_id: parseInt(fromUserId),
                        to_user_id: parseInt(toUserId),
                        ad_id: parseInt(adId),
                        ad_type: adType
                    })
                });

                if (response.ok) {
                    button.style.background = 'linear-gradient(45deg, #32d583, #20bd6d)';
                    button.disabled = true;
                    Utils.showNotification('Соглашение успешно отправлено', 'success');
                } else {
                    throw new Error('Failed to send notification');
                }
            } catch (error) {
                console.error('Error:', error);
                Utils.showNotification('Произошла ошибка', 'error');
            }

            confirmModal.classList.remove('active');
        });
    }

    // Обработчик для кнопки "Нет"
    if (confirmNo) {
        confirmNo.addEventListener('click', () => {
            confirmModal.classList.add('shake');
            setTimeout(() => {
                confirmModal.classList.remove('active', 'shake');
            }, 500);
        });
    }

    // Закрытие при клике вне модального окна
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.confirm-modal') && 
            !e.target.closest('.btn-accept')) {
            confirmModal?.classList.remove('active');
        }
    });
});
</script>

<style>
/* Стили для модального окна и карточек */
.modal-content {
    background: var(--card-bg);
    border: none;
    border-radius: 15px;
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 20px;
}

.modal-body {
    padding: 20px;
}

.details-card {
    color: var(--text-color);
}

.details-header {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.profile-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
}

.header-info h4 {
    margin: 0 0 10px 0;
}

.details-stats {
    display: flex;
    gap: 15px;
    font-size: 14px;
}

.info-item {
    background: var(--gradient-1);
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
}

.social-networks {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 15px;
}

.social-link {
    background: var(--gradient-1);
    padding: 8px 15px;
    border-radius: 20px;
    color: var(--text-color);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.social-link:hover {
    transform: translateY(-2px);
    background: var(--card-hover);
    color: var(--text-color);
}

.description {
    background: var(--gradient-1);
    padding: 15px;
    border-radius: 10px;
}

.description h5 {
    margin-bottom: 10px;
}

/* Стили для модального окна подтверждения */
.confirm-modal {
    position: fixed;
    display: none;
    background: var(--card-bg);
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    transform: translate(-50%, -50%);
}

.confirm-modal.active {
    display: flex;
}

.confirm-content {
    display: flex;
    gap: 15px;
}

.confirm-yes, .confirm-no {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.confirm-yes {
    background: linear-gradient(45deg, #2ecc71, #27ae60);
    color: white;
}

.confirm-no {
    background: linear-gradient(45deg, #e74c3c, #c0392b);
    color: white;
}

.confirm-yes:hover, .confirm-no:hover {
    transform: scale(1.1);
}

.confirm-modal.shake {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
    25% { transform: translate(-50%, -50%) rotate(-5deg); }
    75% { transform: translate(-50%, -50%) rotate(5deg); }
}

/* Стили для уведомлений */
.notification-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    background: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    gap: 10px;
    transform: translateX(120%);
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 1100;
}

.notification-toast.show {
    transform: translateX(0);
    opacity: 1;
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.notification-content i {
    font-size: 20px;
    color: #2ecc71;
}

/* Стили для кнопок действий */
.btn-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-details {
    flex: 1;
    padding: 8px 15px;
    border: none;
    border-radius: 8px;
    background: var(--gradient-1);
    color: var(--text-color);
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-accept {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    background: var(--gradient-1);
    color: var(--text-color);
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-details:hover, .btn-accept:hover {
    transform: translateY(-2px);
    background: var(--card-hover);
}

.btn-accept:disabled {
    background: linear-gradient(45deg, #32d583, #20bd6d);
    cursor: not-allowed;
}
</style> 