class AdsLoader {
    constructor() {
        this.page = 1;
        this.limit = 10;
        this.loading = false;
        this.hasMore = true;
        this.searchQuery = this.getSearchQuery();
    }

    // Получаем поисковый запрос из URL или localStorage
    getSearchQuery() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchFromUrl = urlParams.get('search');
        const searchFromStorage = localStorage.getItem('lastSearchQuery');
        
        // Если есть поисковый запрос в URL, используем его
        if (searchFromUrl) {
            localStorage.setItem('lastSearchQuery', searchFromUrl);
            return searchFromUrl;
        }
        // Если есть сохраненный поиск, используем его
        else if (searchFromStorage) {
            localStorage.removeItem('lastSearchQuery'); // Очищаем после использования
            return searchFromStorage;
        }
        return null;
    }

    async loadAds(type = 'bloggers') {
        if (this.loading || !this.hasMore) return;
        
        this.loading = true;
        const grid = document.querySelector('.products-grid');
        const loadingIndicator = document.getElementById('loadingIndicator');
        
        try {
            loadingIndicator.style.display = 'block';
            
            // Исправляем маппинг типов
            const typeMapping = {
                'bloggers': 'blogger',
                'advertisers': 'company',
                'freelancers': 'freelancer'
            };
            
            const apiType = typeMapping[type] || type;
            
            let url = `https://blogy.uz/api/ads/category/${apiType}?page=${this.page}&limit=${this.limit}`;
            
            // Если есть поисковый запрос, добавляем его к URL
            if (this.searchQuery) {
                url += `&q=${encodeURIComponent(this.searchQuery)}`;
            }

            console.log('Fetching URL:', url); // Для отладки

            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();

            const ads = data.data || [];
            if (ads.length < this.limit) {
                this.hasMore = false;
            }

            // Если это первая загрузка и есть поисковый запрос, очищаем grid
            if (this.page === 1 && this.searchQuery) {
                grid.innerHTML = '';
            }

            ads.forEach(ad => {
                const card = this.createCard(ad);
                if (card) grid.appendChild(card);
            });

            this.page++;

        } catch (error) {
            console.error('Error loading ads:', error);
        } finally {
            this.loading = false;
            loadingIndicator.style.display = 'none';
        }
    }

    createCard(data) {
        const card = document.createElement('div');
        card.className = 'product-card';

        let cardType, title, stats;

        // Определяем тип карточки и её содержимое
        switch(data.type) {
            case 'blogger':
                cardType = 'Блогер';
                title = data.nickname || 'Без имени';
                stats = `
                    <div class="stat-item">
                        <i class="fas fa-users"></i>
                        <span>${this.formatNumber(data.followers)} подписчиков</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-chart-line"></i>
                        <span>ER: ${data.engagement || 0}%</span>
                    </div>
                `;
                break;

            case 'company':
                cardType = 'Компания';
                title = data.name || 'Без названия';
                stats = `
                    <div class="stat-item">
                        <i class="fas fa-briefcase"></i>
                        <span>Бюджет: ${data.budget ? this.formatNumber(data.budget) + '$' : 'Не указан'}</span>
                    </div>
                `;
                break;

            case 'freelancer':
                cardType = 'Фрилансер';
                title = data.name || 'Без имени';
                stats = `
                    <div class="stat-item">
                        <i class="fas fa-laptop-code"></i>
                        <span>${data.category || 'Категория не указана'}</span>
                    </div>
                `;
                break;

            default:
                return null;
        }

        // Единый шаблон для всех типов карточек
        card.innerHTML = `
            <div class="product-image">
                <img src="${data.photo_base64}" alt="${cardType}" onerror="this.src='./img/noImage.jpg'">
                <div class="card-type-badge">${cardType}</div>
            </div>
            <div class="product-info" translate="no">
                <h3 class="notranslate">${title}</h3>
                <div class="category-tag">${data.category || 'Без категории'}</div>
                <div class="stats">
                    ${stats}
                </div>
                <div class="btn-actions">
                    <button class="btn-details" data-id="${data.ID}" data-type="${data.type}">
                        <i class="fas fa-info-circle"></i> Подробнее
                    </button>
                    <button class="btn-accept" data-id="${data.ID}" data-owner-id="${data.user_id}">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        `;

        // Добавляем обработчики событий
        const acceptBtn = card.querySelector('.btn-accept');
        const detailsBtn = card.querySelector('.btn-details');

        acceptBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.showConfirmModal(acceptBtn);
        });

        detailsBtn.addEventListener('click', () => {
            this.showDetails(data.ID, data.type);
        });

        return card;
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

    async showDetails(id, type) {
        try {
            const response = await fetch(`https://blogy.uz/api/ads/details/${type}/${id}`);
            if (!response.ok) throw new Error('Failed to fetch details');
            const data = await response.json();

            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            document.getElementById('detailsContent').innerHTML = this.createDetailsContent(data, type);
            modal.show();
        } catch (error) {
            console.error('Error:', error);
            Utils.showNotification('Ошибка при загрузке данных', 'error');
        }
    }

    createDetailsContent(data, type) {
        // Общий шаблон для деталей
        return `
            <div class="details-card">
                <div class="details-header">
                    <h4>${data.name || data.nickname || 'Без имени'}</h4>
                    ${this.createDetailsStats(data, type)}
                </div>
                <div class="details-info">
                    <div class="info-item">
                        <label><i class="fas fa-tag"></i> Категория</label>
                        <span>${data.category || 'Не указана'}</span>
                    </div>
                    ${this.createSocialLinks(data)}
                    <div class="info-item">
                        <label><i class="fas fa-comment"></i> Комментарий</label>
                        <p class="comment-text">${data.ad_comment || 'Комментарий не добавлен'}</p>
                    </div>
                </div>
            </div>
        `;
    }

    createDetailsStats(data, type) {
        switch(type) {
            case 'blogger':
                return `
                    <div class="details-stats">
                        <span><i class="fas fa-users"></i> ${this.formatNumber(data.followers)} подписчиков</span>
                        <span><i class="fas fa-chart-line"></i> ER: ${data.engagement || 0}%</span>
                    </div>
                `;
            case 'company':
                return `
                    <div class="details-stats">
                        <span><i class="fas fa-briefcase"></i> Бюджет: ${data.budget ? this.formatNumber(data.budget) + '$' : 'Не указан'}</span>
                    </div>
                `;
            default:
                return '';
        }
    }

    createSocialLinks(data) {
        let links = '';
        const socialNetworks = {
            website_link: { icon: 'fas fa-globe', label: 'Веб-сайт' },
            github_link: { icon: 'fab fa-github', label: 'GitHub' },
            telegram_link: { icon: 'fab fa-telegram', label: 'Telegram' },
            instagram_link: { icon: 'fab fa-instagram', label: 'Instagram' },
            youtube_link: { icon: 'fab fa-youtube', label: 'YouTube' }
        };

        for (const [key, value] of Object.entries(socialNetworks)) {
            if (data[key]) {
                links += `
                    <div class="social-network-item">
                        <i class="${value.icon}"></i>
                        <span>${value.label}: <a href="${data[key]}" target="_blank">Перейти</a></span>
                    </div>
                `;
            }
        }

        return links ? `
            <div class="info-item">
                <label><i class="fas fa-share-alt"></i> Социальные сети</label>
                <div class="social-networks">
                    ${links}
                </div>
            </div>
        ` : '';
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    const adsLoader = new AdsLoader();
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') || 'home';
    
    adsLoader.loadAds(currentPage);

    // Бесконечная прокрутка
    window.addEventListener('scroll', () => {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            adsLoader.loadAds(currentPage);
        }
    });
});





