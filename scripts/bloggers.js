class BloggerLoader {
    constructor() {
        this.page = 1;
        this.limit = 10;
        this.loading = false;
        this.hasMore = true;
        this.allBloggers = [];
        this.currentCategory = '';
        this.initializeAcceptButtons();
        this.initializeFilters();
    }

    initializeFilters() {
        const categorySelect = document.getElementById('bloggerCategorySelect');
        if (categorySelect) {
            categorySelect.addEventListener('change', (e) => {
                this.currentCategory = e.target.value;
                this.filterBloggers();
            });
        }
    }

    filterBloggers() {
        const grid = document.querySelector('.products-grid');
        grid.innerHTML = '';

        let filteredBloggers = this.allBloggers;
        
        if (this.currentCategory) {
            console.log('Filtering by category:', this.currentCategory);
            console.log('All bloggers:', this.allBloggers);
            
            filteredBloggers = this.allBloggers.filter(blogger => {
                const bloggerDirection = (blogger.direction || '').toLowerCase().trim();
                const selectedCategory = this.currentCategory.toLowerCase().trim();
                
                console.log('Comparing:', {
                    bloggerDirection,
                    selectedCategory,
                    matches: bloggerDirection === selectedCategory
                });
                
                return bloggerDirection === selectedCategory;
            });
            
            console.log('Filtered bloggers:', filteredBloggers);
        }

        if (filteredBloggers.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.textContent = 'Нет блогеров в выбранной категории';
            grid.appendChild(noResults);
        } else {
            filteredBloggers.forEach(blogger => {
                const card = this.createBloggerCard(blogger);
                grid.appendChild(card);
            });
        }
    }

    initializeAcceptButtons() {
        if (!document.querySelector('.btn-accept')) return;
        
        document.addEventListener('click', async (e) => {
            const acceptBtn = e.target.closest('.btn-accept');
            if (!acceptBtn) return;
            
            e.preventDefault();
            showLoading();
            
            try {
                const adId = acceptBtn.dataset.id;
                const response = await fetch('https://bgweb.nurali.uz/api/user-agreements', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: localStorage.getItem('userId'),
                        ad_id: adId,
                        ad_type: 'blogger'
                    })
                });
                
                if (!response.ok) throw new Error('Network response was not ok');
                
                acceptBtn.disabled = true;
                acceptBtn.textContent = 'Согласие получено';
                acceptBtn.classList.remove('btn-primary');
                acceptBtn.classList.add('btn-success');
            } catch (error) {
                console.error('Error:', error);
                alert('Ошибка при отправке согласия');
            } finally {
                hideLoading();
            }
        });
    }

    async loadBloggers() {
        if (this.loading || !this.hasMore) return;
        
        this.loading = true;
        showLoading();
        const grid = document.querySelector('.products-grid');
        
        try {
            const url = `https://bgweb.nurali.uz/api/ads/category/blogger?page=${this.page}&limit=${this.limit}`;

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            console.log('Loaded bloggers:', data);

            const bloggers = data.data || [];
            const activeBloggers = bloggers.filter(blogger => blogger.status === "true");
            
            if (activeBloggers.length < this.limit) {
                this.hasMore = false;
            }

            this.allBloggers = [...this.allBloggers, ...activeBloggers];
            console.log('Updated allBloggers:', this.allBloggers);

            this.filterBloggers();
            this.page++;

        } catch (error) {
            console.error('Error loading bloggers:', error);
        } finally {
            this.loading = false;
            hideLoading();
        }
    }

    createBloggerCard(data) {
        const card = document.createElement('div');
        card.className = 'product-card animate-card';
        
        const agreementKey = `blogger_${data.id}`;
        const hasAgreement = window.bloggerLoader?.userAgreements?.has(agreementKey);
        
        card.innerHTML = `
            <div class="product-image">
                <img src="${data.photo_base64}" alt="Блогер" onerror="this.src='./img/noImage.jpg'">

            </div>
            <div class="product-info">
                <h3>${data.nickname || 'Без имени'}</h3>
                <div class="category-tag">${data.category || 'Без категории'}</div>
                <div class="direction-tag">${data.direction || ''}</div>

                <div class="btn-actions">
                    <button class="btn-details" data-id="${data.id}" data-type="blogger">
                        <i class="fas fa-info-circle"></i> Подробнее
                    </button>
                    <button class="btn-accept" data-id="${data.id}" data-owner-id="${data.user_id}"
                        ${hasAgreement ? 'disabled style="background: linear-gradient(45deg, #32d583, #20bd6d);"' : ''}>
                        <i class="fas ${hasAgreement ? 'fa-check' : 'fa-check'}"></i>
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
                    <h4>${data.name || 'Без имени'}</h4>
                    <div class="details-stats">
                        <span><i class="fas fa-tag"></i> ${data.category || 'Без категории'}</span>
                        <span><i class="fas fa-compass"></i> ${data.direction || 'Без направления'}</span>
                    </div>
                </div>
                <div class="details-info">
                    <div class="info-item">
                        <label><i class="fas fa-at"></i> Telegram Username</label>
                        <span>${data.telegram_username || 'Не указан'}</span>
                    </div>
                    
                    <div class="info-item">
                        <label><i class="fas fa-search"></i> Кого ищет</label>
                        <p class="looking-for-text">${data.ad_comment || 'Не указано'}</p>
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
                                    <span>tiktok: <a href="${data.tiktok_link}" target="_blank">Перейти</a></span>
                                </div>
                            ` : ''}
                        </div>
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

            console.log('Fetching details for ID:', id);
            const response = await fetch(`https://bgweb.nurali.uz/api/ads/details/blogger/${id}`);
            if (!response.ok) {
                console.error('Response status:', response.status);
                console.error('Response text:', await response.text());
                throw new Error('Failed to fetch details');
            }
            const data = await response.json();
            console.log('Received data:', data);

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

    async loadUserAgreements() {
        const userId = localStorage.getItem('userId');
        if (!userId) return;

        try {
            const response = await fetch(`https://bgweb.nurali.uz/api/user-agreements?user_id=${userId}`);
            if (!response.ok) throw new Error('Failed to fetch agreements');
            const agreements = await response.json();
            
            agreements.forEach(agreement => {
                this.userAgreements.add(`${agreement.ad_type}_${agreement.ad_id}`);
            });
        } catch (error) {
            console.error('Error loading agreements:', error);
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

