class CompanyLoader {
    constructor() {
        this.page = 1;
        this.limit = 20;
        this.loading = false;
        this.hasMore = true;
        this.allCompanies = [];
        this.currentCategory = '';
        this.initializeAcceptButtons();
        this.initializeFilters();
    }

    initializeFilters() {
        const categorySelect = document.getElementById('companyCategorySelect');
        if (categorySelect) {
            categorySelect.addEventListener('change', (e) => {
                this.currentCategory = e.target.value;
                this.filterCompanies();
            });
        }
    }

    filterCompanies() {
        const grid = document.querySelector('.products-grid');
        grid.innerHTML = '';

        let filteredCompanies = this.allCompanies;
        
        if (this.currentCategory) {
            filteredCompanies = this.allCompanies.filter(company => {
                const companyDirection = (company.direction || '').toLowerCase().trim();
                const selectedCategory = this.currentCategory.toLowerCase().trim();
                return companyDirection === selectedCategory;
            });
        }

        if (filteredCompanies.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.textContent = 'Нет компаний в выбранной категории';
            grid.appendChild(noResults);
        } else {
            filteredCompanies.forEach(company => {
                const card = this.createCompanyCard(company);
                grid.appendChild(card);
            });
        }
    }

    async loadCompanies() {
        if (this.loading || !this.hasMore) return;
        
        this.loading = true;
        const grid = document.querySelector('.products-grid');
        const loadingIndicator = document.getElementById('loadingIndicator');
        
        try {
            loadingIndicator.style.display = 'block';
            let url = `http://localhost:8888/api/ads/category/company?page=${this.page}&limit=${this.limit}`;

            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            const companies = data.data || [];
            
            const activeCompanies = companies.filter(company => company.status === "true");
            
            if (activeCompanies.length < this.limit) {
                this.hasMore = false;
            }

            this.allCompanies = [...this.allCompanies, ...activeCompanies];
            
            this.filterCompanies();

            this.page++;

        } catch (error) {
            console.error('Error loading companies:', error);
        } finally {
            this.loading = false;
            loadingIndicator.style.display = 'none';
        }
    }

    initializeAcceptButtons() {
        document.addEventListener('click', async (e) => {
            const acceptBtn = e.target.closest('.btn-accept');
            if (!acceptBtn) return;
            
            e.preventDefault();
            let is_complete = await checkUser();
            if(!is_complete) return;
            showConfirmModal(acceptBtn);

        });
    }

    createCompanyCard(data) {
        const card = document.createElement('div');
        let a = this.formatNumber(data.budget) > 0 ? `Бюджет: $${this.formatNumber(data.budget)}` : "Бартер";
        card.className = 'product-card animate-card';
        card.dataset.id = data.ID;

        card.innerHTML = `
            <div class="product-image">
                <img src="${data.photo_base64}" alt="Компания" onerror="this.src='./img/noImage.jpg'">
               
            </div>
            <div class="product-info">
                <div style="height: 135px;">
                    <h3>${data.name || 'Без имени'}</h3>
                    <div class="direction-tag">${data.direction || 'Без направления'}</div>
                    <div class="stats">
                        <div class="stat-item">
                            <i class="fas fa-briefcase"></i>
                            <span>${a}</span>
                        </div>
                    </div>
                </div>
                <div class="btn-actions">
                    <button class="btn-details" data-id="${data.ID}" data-type="company">
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
        let byu = `Бюджет: ${this.formatNumber(data.budget)}$`
        return `
            <div class="details-card">
                <div class="details-header">
                    <div class="detailImage" style="background-image: url(${data.photo_base64})">
                    </div>
                    <div>
                        <h4>${data.name || 'Без имени'}</h4>
                        
                        <div class="info-item">
                            <span>${data.category || 'Не указана'}</span>
                        </div>
                    </div>
                </div>
                <div class="details-info">
                    <div class="details-stats">
                        <span><i class="fas fa-briefcase"></i> ${this.formatNumber(data.budget) > 0 ? byu : 'Бартер' || 'Не указан'}</span>
                    </div>

                    <div class="info-item">
                        <p class="comment-text">${data.ad_comment || 'Комментарий не добавлен'}</p>
                    </div>
                   
                    ${data.telegram_username ? `
                        <div class="social-network-item">
                            <i class="fab fa-telegram"></i>
                            <span>Telegram: @${data.telegram_username}</span>
                        </div>
                    ` : ''}
                    
                    <div class="info-item sni">
                        <div class="social-networks">

                            ${data.website_link ? `
                                <a class="social-network-item" href="${data.website_link}" target="_blank">
                                    <i class="fas fa-globe"></i>
                                </a>
                            ` : ''}
                            
                            ${data.instagram_link ? `
                                <a class="social-network-item" href="${data.instagram_link}" target="_blank">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            ` : ''}
                            
                            ${data.telegram_link ? `
                                <a class="social-network-item" href="${data.telegram_link}" target="_blank">
                                    <i class="fab fa-telegram"></i>
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

            const response = await fetch(`http://localhost:8888/api/ads/details/company/${id}`);
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
            this.showNotification('Ошибка при загрузке данных', 'error');
        } finally {
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = 'none';
        }
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    const companyLoader = new CompanyLoader();
    companyLoader.loadCompanies();

    // Добавляем обработчик прокрутки для бесконечной загрузки
    window.addEventListener('scroll', () => {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            companyLoader.loadCompanies();
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
            companyLoader.showDetails(id);
        }
    });

}); 