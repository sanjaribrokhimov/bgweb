class BloggerLoader {
    constructor() {
        this.page = 1;
        this.limit = 10;
        this.loading = false;
        this.hasMore = true;
        this.initializeAcceptButtons();
    }

    initializeAcceptButtons() {
        document.addEventListener('click', (e) => {
            const acceptBtn = e.target.closest('.btn-accept');
            if (!acceptBtn) return;
            
            e.preventDefault();
            showConfirmModal(acceptBtn);
        });
    }

    async loadBloggers() {
        if (this.loading || !this.hasMore) return;
        
        this.loading = true;
        const grid = document.querySelector('.products-grid');
        const loadingIndicator = document.getElementById('loadingIndicator');
        
        try {
            loadingIndicator.style.display = 'block';
            const url = `https://bgweb.nurali.uz/api/ads/category/blogger?page=${this.page}&limit=${this.limit}`;

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            const bloggers = data.data || [];
            if (bloggers.length < this.limit) {
                this.hasMore = false;
            }

            const shuffledBloggers = bloggers.sort(() => Math.random() - 0.5);

            shuffledBloggers.forEach(blogger => {
                const card = this.createBloggerCard(blogger);
                grid.appendChild(card);
            });

            this.page++;

        } catch (error) {
            console.error('Error loading bloggers:', error);
        } finally {
            this.loading = false;
            loadingIndicator.style.display = 'none';
        }
    }

    createBloggerCard(data) {
        const card = document.createElement('div');
        card.className = 'product-card animate-card';
        
        card.innerHTML = `
            <div class="product-image">
                <img src="${data.photo_base64}" alt="Блогер" onerror="this.src='./img/noImage.jpg'">
                <div class="card-type-badge">Блогер</div>
            </div>
            <div class="product-info">
                <h3>${data.nickname || 'Без имени'}</h3>
                <div class="category-tag">${data.category || 'Без категории'}</div>
                <div class="stats">
                    <div class="stat-item">
                        <i class="fas fa-users"></i>
                        <span>${this.formatNumber(data.followers)} подписчиков</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-chart-line"></i>
                        <span>ER: ${data.engagement || 0}%</span>
                    </div>
                </div>
                <div class="btn-actions">
                    <button class="btn-details" data-id="${data.ID}" data-type="blogger">
                        <i class="fas fa-info-circle"></i> Подробнее
                    </button>
                    <button class="btn-accept" data-id="${data.ID}" data-owner-id="${data.user_id}">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        `;
        
        return card;
    }

    createDetailsContent(data) {
        return `
            <div class="details-card">
                <div class="details-header">
                    <h4>${data.nickname || 'Без имени'}</h4>
                    <div class="details-stats">
                        <span><i class="fas fa-users"></i> ${this.formatNumber(data.followers)} подписчиков</span>
                        <span><i class="fas fa-chart-line"></i> ER: ${data.engagement || 0}%</span>
                    </div>
                </div>
                <div class="details-info">
                    <div class="info-item">
                        <label><i class="fas fa-tag"></i> Категория</label>
                        <span>${data.category || 'Не указана'}</span>
                    </div>
                    
                    <div class="info-item">
                        <label><i class="fas fa-at"></i> Telegram Username</label>
                        <span>${data.telegram_username || 'Не указан'}</span>
                    </div>
                    
                    <div class="info-item">
                        <label><i class="fas fa-share-alt"></i> Социальные сети</label>
                        <div class="social-networks">
                            ${data.telegram_link ? `
                                <div class="social-network-item">
                                    <i class="fab fa-telegram"></i>
                                    <span>Telegram: <a href="${data.telegram_link}" target="_blank">Перейти</a></span>
                                </div>
                            ` : ''}
                            
                            ${data.instagram_link ? `
                                <div class="social-network-item">
                                    <i class="fab fa-instagram"></i>
                                    <span>Instagram: <a href="${data.instagram_link}" target="_blank">Перейти</a></span>
                                </div>
                            ` : ''}
                            
                            ${data.youtube_link ? `
                                <div class="social-network-item">
                                    <i class="fab fa-youtube"></i>
                                    <span>YouTube: <a href="${data.youtube_link}" target="_blank">Перейти</a></span>
                                </div>
                            ` : ''}
                            
                            ${data.tiktok_link ? `
                                <div class="social-network-item">
                                    <i class="fab fa-tiktok"></i>
                                    <span>TikTok: <a href="${data.tiktok_link}" target="_blank">Перейти</a></span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <label><i class="fas fa-comment"></i> Комментарий</label>
                        <p class="comment-text">${data.ad_comment || 'Комментарий не добавлен'}</p>
                    </div>
                </div>
            </div>
        `;
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

    async showDetails(id) {
        try {
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = 'block';

            const response = await fetch(`https://bgweb.nurali.uz/api/ads/details/blogger/${id}`);
            if (!response.ok) throw new Error('Failed to fetch details');
            const data = await response.json();

            const modalHTML = `
                <div class="modal fade custom-modal" id="detailsModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Подробная информация</h5>
                                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="detailsContent">
                                    ${this.createDetailsContent(data)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const styles = `
                .custom-modal .modal-content {
                    background: #2c2c2c;
                    border: 1px solid rgba(255,255,255,0.1);
                    border-radius: 20px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
                }

                .custom-modal .modal-header {
                    background: #333333;
                    border-bottom: 1px solid rgba(255,255,255,0.1);
                    border-radius: 20px 20px 0 0;
                    padding: 20px 30px;
                    position: relative;
                    padding-right: 50px;
                }

                .modal-close-btn {
                    position: absolute;
                    right: 20px;
                    top: 20px;
                    width: 30px;
                    height: 30px;
                    border: none;
                    background: rgba(255, 255, 255, 0.1);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    color: #fff;
                    font-size: 16px;
                }

                .modal-close-btn:hover {
                    background: rgba(255, 255, 255, 0.2);
                    transform: rotate(90deg);
                }

                .custom-modal .modal-body {
                    
                    border-radius: 0 0 20px 20px;
                    padding: 30px;
                }

                .info-item {
                    background: #333333;
                    border-radius: 12px;
                    padding: 15px;
                    margin-bottom: 15px;
                    transition: all 0.3s ease;
                }

                .info-item:hover {
                    background: #3c3c3c;
                    transform: translateY(-2px);
                }

                .social-network-item {
                    background: #333333;
                    padding: 12px;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                    margin-bottom: 8px;
                }

                .social-network-item:hover {
                    background: #3c3c3c;
                    transform: translateX(5px);
                }
            `;

            const styleElement = document.createElement('style');
            styleElement.textContent = styles;
            document.head.appendChild(styleElement);

            const oldModal = document.getElementById('detailsModal');
            if (oldModal) {
                oldModal.remove();
            }

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            modal.show();

        } catch (error) {
            console.error('Error:', error);
            const notification = document.createElement('div');
            notification.className = 'alert alert-danger';
            notification.textContent = 'Ошибка при загрузке данных';
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        } finally {
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = 'none';
        }
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    const bloggerLoader = new BloggerLoader();
    bloggerLoader.loadBloggers();

    window.addEventListener('scroll', () => {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            bloggerLoader.loadBloggers();
        }
    });

    document.addEventListener('click', (e) => {
        if (e.target.closest('.btn-details')) {
            const button = e.target.closest('.btn-details');
            const id = button.dataset.id;
            bloggerLoader.showDetails(id);
        }
    });
});
