class BloggerLoader {
    constructor() {
        this.page = 1;
        this.limit = 10;
        this.loading = false;
        this.hasMore = true;
        this.allBloggers = [];
        this.currentCategory = '';
        this.totalPages = 0;
        this.initializeAcceptButtons();
        this.initializeFilters();
        this.initializePagination();
        this.initializeInfiniteScroll();
        this.loadBloggers();
    }

    initializeFilters() {
        const categorySelect = document.getElementById('bloggerCategorySelect');
        if (categorySelect) {
            categorySelect.addEventListener('change', (e) => {
                const grid = document.querySelector('.products-grid');
                grid.innerHTML = ''; // Очищаем грид перед сменой категории
                
                this.currentCategory = e.target.value;
                this.page = 1;
                this.hasMore = true;
                this.loadBloggers();
            });
        }
    }

    initializeInfiniteScroll() {
        window.addEventListener('scroll', () => {
            // Проверяем, достиг ли пользователь конца страницы
            if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 500) {
                if (!this.loading && this.hasMore) {
                    this.loadBloggers();
                }
            }
        });
    }

    filterBloggers() {
        const grid = document.querySelector('.products-grid');
        
        let filteredBloggers = this.allBloggers;
        
        if (this.currentCategory) {
            filteredBloggers = this.allBloggers.filter(blogger => {
                const bloggerDirection = (blogger.direction || '').toLowerCase().trim();
                const selectedCategory = this.currentCategory.toLowerCase().trim();
                return bloggerDirection === selectedCategory;
            });
        }

        if (filteredBloggers.length === 0) {
            console.log(this.allBloggers)
            grid.innerHTML = '<div class="no-results">Нет блогеров в выбранной категории</div>';
        } else {
            // Получаем только новые карточки (последние this.limit элементов)
            const newBloggers = filteredBloggers.slice(-this.limit);
            
            // Создаем HTML для новых карточек
            const newCardsHTML = newBloggers
                .map(blogger => this.createBloggerCardHTML(blogger))
                .join('');
            
            // Если это первая страница, заменяем содержимое
            if (this.page === 1) {
                grid.innerHTML = newCardsHTML;
            } else {
                // Иначе добавляем новые карточки в конец
                grid.insertAdjacentHTML('beforeend', newCardsHTML);
            }
        }
    }

    initializeAcceptButtons() {
        document.addEventListener('click', (e) => {
            const acceptBtn = e.target.closest('.btn-accept');
            if (!acceptBtn) return;
            
            e.preventDefault();
            showConfirmModal(acceptBtn);
        });
    }

    initializePagination() {
        // Создаем контейнер для пагинации если его нет
        if (!document.querySelector('.pagination-container')) {
            const container = document.createElement('div');
            container.className = 'pagination-container';
            document.querySelector('.products-grid').insertAdjacentElement('afterend', container);
        }
    }

    createPaginationControls(currentPage, totalPages) {
        const container = document.querySelector('.pagination-container');
        container.innerHTML = '';

        const pagination = document.createElement('div');
        pagination.className = 'pagination';

        // Кнопка "Назад"
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevBtn.className = 'pagination-btn';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => this.changePage(currentPage - 1);

        // Кнопка "Вперед"
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextBtn.className = 'pagination-btn';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => this.changePage(currentPage + 1);

        // Добавляем номера страниц
        const pageNumbers = document.createElement('div');
        pageNumbers.className = 'page-numbers';

        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || // Первая страница
                i === totalPages || // Последняя страница
                (i >= currentPage - 1 && i <= currentPage + 1) // Текущая и соседние
            ) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                pageBtn.className = `page-number ${i === currentPage ? 'active' : ''}`;
                pageBtn.onclick = () => this.changePage(i);
                pageNumbers.appendChild(pageBtn);
            } else if (
                (i === currentPage - 2 && currentPage > 3) ||
                (i === currentPage + 2 && currentPage < totalPages - 2)
            ) {
                const dots = document.createElement('span');
                dots.textContent = '...';
                dots.className = 'pagination-dots';
                pageNumbers.appendChild(dots);
            }
        }

        pagination.appendChild(prevBtn);
        pagination.appendChild(pageNumbers);
        pagination.appendChild(nextBtn);
        container.appendChild(pagination);
    }

    async changePage(newPage) {
        this.page = newPage;
        await this.loadBloggers();
    }

    async loadBloggers() {
        try {
            if (this.loading || !this.hasMore) return;
            
            this.loading = true;
            
            // Формируем URL с параметрами
            let url = `http://localhost:8888/api/post-bloggers/paginated?page=${this.page}&limit=${this.limit}`;
            if (this.currentCategory && this.currentCategory !== 'all') {
                url += `&category=${this.currentCategory}`;
            }

            const response = await fetch(url);
            const data = await response.json();
            
            if (data && data.posts) {
                const grid = document.querySelector('.products-grid');
                
                // Если постов нет, показываем сообщение
                if (data.posts.length === 0 && this.page === 1) {
                    grid.innerHTML = '<div class="no-results">Нет блогеров в выбранной категории</div>';
                    this.hasMore = false;
                    return;
                }

                // Создаем HTML для новых карточек
                const newCardsHTML = data.posts
                    .map(blogger => this.createBloggerCardHTML(blogger))
                    .join('');
                
                // Если это первая страница, заменяем содержимое
                if (this.page === 1) {
                    grid.innerHTML = newCardsHTML;
                } else {
                    // Иначе добавляем новые карточки в конец
                    grid.insertAdjacentHTML('beforeend', newCardsHTML);
                }

                this.hasMore = this.page < data.totalPages;
                this.page++;
            }
        } catch (error) {
            console.error('Error loading bloggers:', error);
            if (this.page === 1) {
                const grid = document.querySelector('.products-grid');
                grid.innerHTML = '<div class="error-message">Ошибка загрузки данных</div>';
            }
        } finally {
            this.loading = false;
        }
    }

    createBloggerCardHTML(data) {
        const agreementKey = `blogger_${data.id}`;
        const hasAgreement = window.bloggerLoader?.userAgreements?.has(agreementKey);
        
        return `
            <div class="product-card animate-card" data-id="${data.id}">
                <div class="product-image">
                    <img id="myImg" src="${data.photo_base64}" alt="Блогер" onerror="this.src='./img/noImage.jpg'">
                </div>
                <div class="product-info">
                    <div>
                        <p style="color:var(--accent-orange); padding:0px; margin:0px; max-width: 168px;">${data.nickname || 'Без имени'}</p>
                        <div class="direction-tag">${data.direction || ''}</div>
                    </div>

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
            </div>
        `;
    }

    createDetailsContent(data) {
        return `
            <div class="details-card">
                <div class="details-header">
                    <div class="detailImage" style="background-image: url(${data.photo_base64})"></div>
                    <div>
                        <h4>${data.nickname || 'Без имени'}</h4>
                        
                        <div class="info-item">
                            <span>${data.direction || 'Без направления'}</span>
                        </div>
                    </div>
                </div>
                <div class="details-info">
                    
                    
                    <div class="info-item">
                        <label><i class="fas fa-search"></i> Кого ищет</label>
                        <p class="looking-for-text">${data.ad_comment || 'Не указано'}</p>
                    </div>
                    
                    <div class="info-item sni">
                        <div class="social-networks">                         
                            ${data.telegram_link ? `
                                <a class="social-network-item" href="${data.telegram_link}" target="_blank">
                                    <i class="fab fa-telegram"></i>
                                </a>
                            ` : ''}
                            ${data.instagram_link ? `
                                <a class="social-network-item" href="${data.instagram_link}" target="_blank">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            ` : ''}
                            ${data.youtube_link ? `
                                <a class="social-network-item" href="${data.youtube_link}" target="_blank">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            ` : ''}
                            ${data.tiktok_link ? `
                                <a class="social-network-item" href="${data.tiktok_link}" target="_blank">
                                    <i class="fab fa-tiktok"></i>
                                </a>
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

            // console.log('Fetching details for ID:', id);
            const response = await fetch(`http://localhost:8888/api/ads/details/blogger/${id}`);
            if (!response.ok) {
                console.error('Response status:', response.status);
                console.error('Response text:', await response.text());
                throw new Error('Failed to fetch details');
            }
            const data = await response.json();
            // console.log('Received data:', data);


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
                    background: var(--gradient-2);
                    border: 1px solid rgba(255,255,255,0.1);
                    border-radius: 20px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
                }

                .custom-modal .modal-header {
                    
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
                  
                    font-size: 16px;
                }

                .modal-close-btn:hover {
                    
                    transform: rotate(90deg);
                }

                .info-item {
                    
                    border-radius: 12px;
                    padding: 15px;
                    margin-bottom: 15px;
                    transition: all 0.3s ease;
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
            const response = await fetch(`http://localhost:8888/api/user-agreements?user_id=${userId}`);
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

// Добавляем стили для индикатора загрузки
const style = document.createElement('style');
style.textContent = `
    .load-more {
        display: flex;
        justify-content: center;
        padding: 20px;
        color: var(--text-color);
        font-size: 24px;
    }

    .load-more i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .error-message {
        text-align: center;
        padding: 20px;
        color: var(--text-color);
        background: var(--card-bg);
        border-radius: 8px;
        margin: 20px;
    }
`;
document.head.appendChild(style);

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    const bloggerLoader = new BloggerLoader();
    bloggerLoader.loadBloggers();

    // Обработчик прокрутки
    window.addEventListener('scroll', () => {
        const loadMore = document.querySelector('.load-more');
        if (!loadMore) return;

        const rect = loadMore.getBoundingClientRect();
        if (rect.top <= window.innerHeight && !bloggerLoader.loading) {
            bloggerLoader.loadBloggers();
        }
    });

    document.addEventListener('click', (e) => {
        // Проверяем, не является ли целевой элемент кнопкой .btn-accept
        if (e.target.closest('.btn-accept')) {
            return;
        }
        if (e.target.closest('.btn-details') || e.target.closest('.product-card')) {
            const thisTarget = e.target.closest('.btn-details') || e.target.closest('.product-card');
            const id = thisTarget.dataset.id;
            bloggerLoader.showDetails(id);
        }
    });
});

// Добавляем стили для пагинации
const stylePagination = document.createElement('style');
stylePagination.textContent = `
    .pagination-container {
        display: flex;
        justify-content: center;
        margin: 20px 0;
        padding: 20px;
    }

    .pagination {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .pagination-btn {
        background: var(--card-bg);
        border: 1px solid rgba(255,255,255,0.1);
        color: var(--text-color);
        padding: 8px 15px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-btn:not(:disabled):hover {
        background: var(--card-hover);
    }

    .page-numbers {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .page-number {
        background: var(--card-bg);
        border: 1px solid rgba(255,255,255,0.1);
        color: var(--text-color);
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .page-number.active {
        background: var(--accent-blue);
        color: white;
    }

    .page-number:not(.active):hover {
        background: var(--card-hover);
    }

    .pagination-dots {
        color: var(--text-color);
        padding: 0 5px;
    }
`;
document.head.appendChild(stylePagination);

