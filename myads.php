<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="profile.myAds">My Ads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'components/miniHeader.php'; ?>

    <!-- Подключаем необходимые скрипты -->
    <script src="translations.js"></script>
    <script src="scripts/utils.js"></script>
    <script src="scripts/myads.js"></script>

    <div class="container mt-4">
        <div class="profile-page">
            <!-- Информация о пользователе -->
            <div class="profile-header mb-4">
                <div class="user-info">
                    <div class="info">
                        <h4 class="user-name" id="userName" data-translate="myads.loading">Загрузка...</h4>
                        <p class="user-email" id="userEmail" data-translate="myads.loading">Загрузка...</p>
                        <span class="user-category" id="userCategory" data-translate="myads.loading">Загрузка...</span>
                    </div>
                </div>
            </div>

            <!-- Объявления -->
            <div class="tab-content">
                <div class="tab-pane active" id="my-ads">
                    <div class="products-grid" id="adsContainer">
                        <!-- Объявления будут добавлены через JavaScript -->
                    </div>
                    <div id="loadingIndicator" class="text-center" style="display: none;">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden" data-translate="myads.loading">Загрузка...</span>
                        </div>
                    </div>
                    <div id="noAdsMessage" style="display: none;" class="text-center mt-4">
                        <p data-translate="myads.noAds">У вас пока нет объявлений</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" data-translate="myads.deleteConfirmTitle">Подтверждение действия</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-exclamation-circle text-warning mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-0" data-translate="myads.deleteConfirmText">Вы действительно хотите удалить это объявление?</p>
                    <p class="text-muted small" data-translate="myads.deleteConfirmSubtext">Это действие нельзя будет отменить</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" data-translate="myads.cancel">Отмена</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmDelete" data-translate="myads.delete">Удалить</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    class MyAdsLoader {
        constructor() {
            this.userId = localStorage.getItem('userId');
            this.loadUserInfo();
            this.loadUserAds();
        }

        async loadUserInfo() {
            try {
                const userId = localStorage.getItem('userId');
                console.log('Loading user info for ID:', userId); // Для отладки
                
                const response = await fetch(`https://bgweb.nurali.uz/api/auth/user/${userId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to fetch user info');
                }
                
                const data = await response.json();
                console.log('User data:', data); // Для отладки
                
                // Обновляем информацию о пользователе на странице
                document.getElementById('userName').textContent = data.name || 'Без имени';
                document.getElementById('userEmail').textContent = data.email || '';
                document.getElementById('userCategory').textContent = data.category || 'Без категории';
                
            } catch (error) {
                console.error('Error loading user info:', error);
                Utils.showNotification('Ошибка при загрузке информации о пользователе', 'error');
            }
        }

        async loadUserAds() {
            try {
                console.log('Loading ads for user ID:', this.userId); // Для отладки
                const response = await fetch(`https://bgweb.nurali.uz/api/ads/user/${this.userId}`);
                
                if (!response.ok) {
                    throw new Error('Failed to fetch user ads');
                }
                
                const data = await response.json();
                console.log('User ads:', data); // Для отладки
                
                const adsContainer = document.getElementById('adsContainer');
                const noAdsMessage = document.getElementById('noAdsMessage');
                
                if (!data || data.length === 0) {
                    noAdsMessage.style.display = 'block';
                    return;
                }

                adsContainer.innerHTML = '';
                data.forEach(ad => {
                    const card = this.createAdCard(ad);
                    adsContainer.appendChild(card);
                });
                
            } catch (error) {
                console.error('Error loading user ads:', error);
                Utils.showNotification('Ошибка при загрузке объявлений', 'error');
            }
        }

        createAdCard(ad) {
            const card = document.createElement('div');
            card.className = 'product-card';
            
            card.innerHTML = `
                <div class="product-image">
                    <img src="${ad.photo_base64 || './img/noImage.jpg'}" 
                         alt="Ad Image" 
                         onerror="this.src='./img/noImage.jpg'">
                    <div class="card-type-badge">${this.getAdTypeLabel(ad.type)}</div>
                </div>
                <div class="product-info">
                    <h3>${ad.name || ad.nickname || 'Без названия'}</h3>
                    <div class="category-tag">${ad.category || 'Без категории'}</div>
                    ${this.getAdStats(ad)}
                    <div class="btn-actions">
                        <button class="btn-delete" data-id="${ad.id}">
                            <i class="fas fa-trash"></i> Удалить
                        </button>
                    </div>
                </div>
            `;

            // Добавляем обработчик для кнопки удаления
            const deleteBtn = card.querySelector('.btn-delete');
            deleteBtn.addEventListener('click', () => this.showDeleteConfirm(ad.id));

            return card;
        }

        getAdTypeLabel(type) {
            const labels = {
                'blogger': 'Блогер',
                'company': 'Компания',
                'freelancer': 'Фрилансер'
            };
            return labels[type] || type;
        }

        getAdStats(ad) {
            if (ad.type === 'blogger') {
                return `
                    <div class="stats">
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span>${this.formatNumber(ad.followers)} подписчиков</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-chart-line"></i>
                            <span>ER: ${ad.engagement || 0}%</span>
                        </div>
                    </div>
                `;
            }
            if (ad.type === 'company') {
                return `
                    <div class="stats">
                        <div class="stat-item">
                            <i class="fas fa-briefcase"></i>
                            <span>Бюджет: ${this.formatNumber(ad.budget)}$</span>
                        </div>
                    </div>
                `;
            }
            return '';
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

        async showDeleteConfirm(adId) {
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            const confirmDeleteBtn = document.getElementById('confirmDelete');
            
            confirmDeleteBtn.onclick = async () => {
                try {
                    const response = await fetch(`https://bgweb.nurali.uz/api/ads/delete/${adId}`, {
                        method: 'DELETE'
                    });

                    if (response.ok) {
                        Utils.showNotification('Объявление успешно удалено', 'success');
                        await this.loadUserAds(); // Перезагружаем список объявлений
                    } else {
                        throw new Error('Failed to delete ad');
                    }
                } catch (error) {
                    console.error('Error deleting ad:', error);
                    Utils.showNotification('Ошибка при удалении объявления', 'error');
                }
                modal.hide();
            };

            modal.show();
        }
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', () => {
        new MyAdsLoader();
    });
    </script>
</body>
</html> 