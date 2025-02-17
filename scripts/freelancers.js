class FreelancerLoader {
    constructor() {
        this.page = 1;
        this.limit = 20;
        this.loading = false;
        this.hasMore = true;
        this.allFreelancers = [];
        this.currentCategory = '';
        this.initializeAcceptButtons();
        this.initializeFilters();
    }

    initializeAcceptButtons() {
        document.addEventListener('click', (e) => {
            const acceptBtn = e.target.closest('.btn-accept');
            if (!acceptBtn) return;
            
            e.preventDefault();
            showConfirmModal(acceptBtn);
        });
    }

    initializeFilters() {
        const categorySelect = document.getElementById('freelancerCategorySelect');
        if (categorySelect) {
            categorySelect.addEventListener('change', (e) => {
                // console.log('Selected category:', e.target.value);
                this.currentCategory = e.target.value;
                this.filterFreelancers();
            });
        }
    }

    filterFreelancers() {
        const grid = document.querySelector('.products-grid');
        grid.innerHTML = '';

        let filteredFreelancers = this.allFreelancers;
        
        if (this.currentCategory) {
            // console.log('Filtering by category:', this.currentCategory);
            // console.log('All freelancers:', this.allFreelancers);
            
            filteredFreelancers = this.allFreelancers.filter(freelancer => {
                const freelancerCategory = freelancer.category.toLowerCase().trim();
                const selectedCategory = this.currentCategory.toLowerCase().trim();
                
                const categoryMatches = freelancerCategory === selectedCategory;
                
                // console.log('Comparing categories:', {
                //     freelancerCategory,
                //     selectedCategory,
                //     matches: categoryMatches
                // });

                
                return categoryMatches;
            });
            
            // console.log('Filtered freelancers:', filteredFreelancers);
        }


        if (filteredFreelancers.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.textContent = 'Нет фрилансеров в выбранной категории';
            grid.appendChild(noResults);
        } else {
            filteredFreelancers.forEach(freelancer => {
                const card = this.createFreelancerCard(freelancer);
                grid.appendChild(card);
            });
        }
    }

    async loadFreelancers() {
        if (this.loading || !this.hasMore) return;
        
        this.loading = true;
        const grid = document.querySelector('.products-grid');
        const loadingIndicator = document.getElementById('loadingIndicator');
        
        try {
            loadingIndicator.style.display = 'block';
            const url = `http://localhost:8888/api/ads/category/freelancer?page=${this.page}&limit=${this.limit}`;

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            // console.log('Loaded freelancers:', data);


            const freelancers = data.data || [];
            const activeFreelancers = freelancers.filter(freelancer => freelancer.status === "true");
            
            if (activeFreelancers.length < this.limit) {
                this.hasMore = false;
            }

            this.allFreelancers = [...this.allFreelancers, ...activeFreelancers];
            // console.log('Updated allFreelancers:', this.allFreelancers);


            this.filterFreelancers();

            this.page++;

        } catch (error) {
            console.error('Error loading freelancers:', error);
        } finally {
            this.loading = false;
            loadingIndicator.style.display = 'none';
        }
    }

    createFreelancerCard(data) {
        const card = document.createElement('div');
        card.className = 'product-card animate-card';
        card.dataset.id = data.ID;
        const itemId = data.ID || data.id;
        const userId = data.user_id || data.userId;
        
        // console.log('Creating freelancer card with data:', data);
        

        card.innerHTML = `
            <div class="product-image">
                <img src="${data.photo_base64}" alt="Фрилансер" onerror="this.src='./img/noImage.jpg'">
                
            </div>
            <div class="product-info">
                <h3>${data.name || 'Без имени'}</h3>
               
                <div class="stats">
                    <div class="stat-item">
                        <i class="fas fa-laptop-code"></i>
                        <span>${data.category}</span>
                    </div>
                </div>
                <div class="btn-actions">
                    <button class="btn-details" data-id="${itemId}" data-type="freelancer">
                        <i class="fas fa-info-circle"></i> Подробнее
                    </button>
                    <button class="btn-accept" data-id="${itemId}" data-owner-id="${userId}">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        `;
        
        // console.log('Freelancer card data:', {
        //     id: itemId,
        //     userId: userId,
        //     type: 'freelancer'
        // });

        
        return card;
    }

    createDetailsContent(data) {
        console.log(data)
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
                    
                    <div class="info-item">
                        <p class="comment-text">${data.ad_comment || 'Комментарий не добавлен'}</p>
                    </div>
                    
                    <div class="info-item sni">
                        <div class="social-networks">
                            ${data.github_link ? `
                                <a class="social-network-item" href="${data.github_link}" target="_blank">
                                    <i class="fab fa-github"></i>
                                </a>
                            ` : ''}
                            
                            ${data.portfolio_link ? `
                                <a class="social-network-item" href="${data.portfolio_link}" target="_blank">
                                    <i class="fas fa-briefcase"></i>
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
                            
                            ${data.youtube_link ? `
                                <a class="social-network-item" href="${data.youtube_link}" target="_blank">
                                    <i class="fab fa-youtube"></i>
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

            const response = await fetch(`http://localhost:8888/api/ads/details/freelancer/${id}`);
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
    const freelancerLoader = new FreelancerLoader();
    freelancerLoader.loadFreelancers();

    window.addEventListener('scroll', () => {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            freelancerLoader.loadFreelancers();
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
            freelancerLoader.showDetails(id);
        }
    });
}); 