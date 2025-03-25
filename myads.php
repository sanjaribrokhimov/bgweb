<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="profile.myAds">My Ads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1.1.5">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Стили для модального окна */
        .modal-content {
            border-radius: 15px;
            background: var(--bs-body-bg);
            color: var(--bs-body-color);
        }

        .delete-icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            border-radius: 50%;
            background: var(--bs-danger-bg-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .delete-icon-wrapper i {
            font-size: 2rem;
        }

        .delete-title {
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--bs-body-color);
        }

        /* Темная тема */
        [data-bs-theme="dark"] .modal-content {
            background: var(--bs-dark);
            border: 1px solid var(--bs-dark-border-subtle);
        }

        [data-bs-theme="dark"] .btn-outline-secondary {
            border-color: var(--bs-dark-border-subtle);
            color: var(--bs-body-color);
        }

        [data-bs-theme="dark"] .btn-outline-secondary:hover {
            background: var(--bs-dark-border-subtle);
            border-color: var(--bs-dark-border-subtle);
        }

        [data-bs-theme="dark"] .delete-icon-wrapper {
            background: rgba(var(--bs-danger-rgb), 0.2);
        }

        /* Анимации */
        .modal.fade .modal-dialog {
            transform: scale(0.8);
            transition: transform 0.3s ease-out;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }

        .delete-icon-wrapper i {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: rotate(0); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
    </style>
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
                        <h4 class="user-name translate" id="userName" data-translate="myads.loading">Загрузка...</h4>
                        <p class="user-email translate" id="userEmail" data-translate="myads.loading">Загрузка...</p>
                        <span class="user-category translate" id="userCategory" data-translate="myads.loading">Загрузка...</span>
                    </div>
                </div>
            </div>

            <!-- Объявления -->
            <div class="tab-content">
                <div class="tab-pane active" id="my-ads">
                    <div class="products-gridd" id="adsContainer">
                        <!-- Объявления будут добавлены через JavaScript -->
                    </div>
                    <div id="loadingIndicator" class="text-center" style="display: none;">
                        <div class="spinner-border" role="status">
                        </div>
                    </div>
                    <div id="noAdsMessage" style="display: none;" class="text-center mt-4">
                        <p data-translate="myads.noAds" class="translate">У вас пока нет объявлений</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 text-center">
                    <h5 class="modal-title w-100 translate" data-translate="myads.deleteConfirmTitle">Подтверждение действия</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="delete-icon-wrapper mb-4">
                        <i class="fas fa-trash-alt text-danger"></i>
                    </div>
                    <h4 class="delete-title mb-3 translate" data-translate="myads.deleteConfirmText">
                        Вы действительно хотите удалить это объявление?
                    </h4>
                    <p class="text-muted translate" data-translate="myads.deleteConfirmSubtext">
                        Это действие нельзя будет отменить
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" data-translate="myads.cancel">
                        <i class="fas fa-times me-2"></i><span class="translate">Отмена</span>
                    </button>
                    <button type="button" class="btn btn-danger px-4 py-2" id="confirmDelete" data-translate="myads.delete">
                        <i class="fas fa-trash-alt me-2"></i><span class="translate">Удалить</span>
                    </button>
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
        }

        async loadUserInfo() {
            try {
                const token = localStorage.getItem('userId');
                if (!this.userId || !token) {
                    throw new Error('User ID or token not found');
                }
                
                const response = await fetch(`https://blogy.uz/api/auth/user/${this.userId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to fetch user info');
                }
                
                const data = await response.json();
                
                // Обновляем информацию о пользователе на странице
                document.getElementById('userName').textContent = data.name || 'Без имени';
                document.getElementById('userEmail').textContent = data.email || '';
                document.getElementById('userCategory').textContent = data.category || 'Без категории';
                
            } catch (error) {
                console.error('Error loading user info:', error);
                Utils.showNotification('Ошибка при загрузке информации о пользователе', 'error');
            }
        }
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', () => {
        new MyAdsLoader();
    });
    </script>
</body>
</html> 