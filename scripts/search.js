class Search {
    constructor() {
        // Инициализация элементов
        this.searchInput = document.getElementById('searchInput');
        this.searchIcon = document.getElementById('searchIcon');
        this.searchResults = document.getElementById('searchResults');
        this.searchContainer = document.querySelector('.search-container');

        if (!this.searchInput || !this.searchIcon || !this.searchContainer) {
            console.error('Не найдены необходимые элементы поиска');
            return;
        }

        this.searchTimeout = null;
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Обработчик клика по иконке поиска
        this.searchIcon.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Клик по иконке поиска'); // Для отладки
            
            this.searchInput.classList.toggle('active');
            this.searchContainer.classList.toggle('search-active');
            
            if (this.searchInput.classList.contains('active')) {
                this.searchInput.focus();
            } else {
                this.searchInput.value = '';
                this.hideResults();
            }
        });

        // Обработчик ввода в поле поиска
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length >= 2) {
                this.searchTimeout = setTimeout(() => this.performSearch(query), 300);
            } else {
                this.hideResults();
            }
        });

        // Закрытие при клике вне области поиска
        document.addEventListener('click', (e) => {
            if (!this.searchContainer.contains(e.target)) {
                this.searchInput.classList.remove('active');
                this.searchContainer.classList.remove('search-active');
                this.hideResults();
            }
        });

        // Предотвращение закрытия при клике на поле ввода
        this.searchInput.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    async performSearch(query) {
        try {
            const response = await fetch(`http://localhost:8888/api/ads/search?q=${encodeURIComponent(query)}`);
            if (!response.ok) {
                throw new Error('Ошибка сети');
            }
            const data = await response.json();
            
            // Добавляем логирование
            console.log('Поисковый запрос:', query);
            console.log('Ответ от сервера:', data);
            console.log('Результаты поиска:', data.results);
            
            this.displayResults(data, query);
        } catch (error) {
            console.error('Ошибка поиска:', error);
            this.showError();
        }
    }

    displayResults(data, query) {
        if (!this.searchResults) return;

        this.searchResults.innerHTML = '';
        this.searchResults.style.display = 'block';

        if (!data.results || data.total === 0) {
            this.searchResults.innerHTML = '<div class="search-no-results">Ничего не найдено</div>';
            return;
        }

        const typeLabels = {
            bloggers: 'Блогер',
            companies: 'Компания',
            freelancers: 'Фрилансер'
        };

        Object.entries(data.results).forEach(([type, items]) => {
            if (items && items.length > 0) {
                items.forEach(item => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'search-result-item';
                    
                    const photoUrl = item.data.photo_base64 || './img/noImage.jpg';

                    resultItem.innerHTML = `
                        <img src="${photoUrl}" 
                             onerror="this.src='./img/noImage.jpg'" 
                             alt="${item.data.name || item.data.nickname || 'Фото профиля'}">
                        <div class="search-result-info">
                            <div class="search-result-name">${item.data.name || item.data.nickname || 'Без имени'}</div>
                            <div class="search-result-type">${typeLabels[type]} • ${item.data.category || 'Без категории'}</div>
                        </div>
                    `;

                    resultItem.addEventListener('click', () => {
                        localStorage.setItem('searchQuery', query);
                        localStorage.setItem('searchType', type);
                        localStorage.setItem('selectedItemId', item.data.id);
                        
                        window.location.href = `search-results.php?q=${encodeURIComponent(query)}&type=${type}&selected=${item.data.id}`;
                    });

                    this.searchResults.appendChild(resultItem);
                });
            }
        });
    }

    showError() {
        if (!this.searchResults) return;
        this.searchResults.innerHTML = '<div class="search-no-results">Произошла ошибка при поиске</div>';
        this.searchResults.style.display = 'block';
    }

    hideResults() {
        if (!this.searchResults) return;
        this.searchResults.style.display = 'none';
    }
}

// Инициализация поиска при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    new Search();
}); 