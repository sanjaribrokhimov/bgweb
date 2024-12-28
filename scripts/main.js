// Определяем функцию глобально
window.showConfirmModal = null;

document.addEventListener('DOMContentLoaded', () => {
    // Находим модальное окно
    const confirmModal = document.querySelector('.confirm-modal');
    const confirmYes = confirmModal?.querySelector('.confirm-yes');
    const confirmNo = confirmModal?.querySelector('.confirm-no');

    if (!confirmModal || !confirmYes || !confirmNo) {
        console.error('Modal elements not found');
        return;
    }

    // Инициализируем bloggerLoader если его нет
    if (!window.bloggerLoader) {
        window.bloggerLoader = {
            userAgreements: new Set()
        };
        // Сразу загружаем соглашения
        loadUserAgreements();
    }

    // Функция загрузки соглашений пользователя
    async function loadUserAgreements() {
        const userId = localStorage.getItem('userId');
        if (!userId) return;

        try {
            const response = await fetch(`https://blogy.uz/api/user-agreements?user_id=${userId}`);
            if (!response.ok) throw new Error('Failed to fetch agreements');
            
            const agreements = await response.json();
            agreements.forEach(agreement => {
                window.bloggerLoader.userAgreements.add(`${agreement.ad_type}_${agreement.ad_id}`);
            });
        } catch (error) {
            console.error('Error loading agreements:', error);
        }
    }

    // Определяем глобальную функцию для показа модального окна
    window.showConfirmModal = (button) => {
        const rect = button.getBoundingClientRect();
        confirmModal.style.top = `${rect.top}px`;
        confirmModal.style.left = `${rect.left}px`;
        confirmModal.classList.add('active');
        confirmModal.currentButton = button;
    };

    // Обработчик для кнопки "Да"
    confirmYes.addEventListener('click', async () => {
        const button = confirmModal.currentButton;
        if (!button) return;

        // Сначала закрываем модалку
        confirmModal.classList.remove('active');
        
        // Сохраняем оригинальные элементы кнопки
        const originalHTML = button.innerHTML;
        const originalBackground = button.style.background;
        
        // Показываем индикатор загрузки
        button.innerHTML = `
            <div class="spinner-wrapper">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        `;
        button.style.background = 'var(--gradient-1)';
        button.disabled = true;

        try {
            const adId = button.dataset.id;
            const fromUserId = localStorage.getItem('userId');
            const toUserId = button.dataset.ownerId;
            const detailsBtn = button.closest('.btn-actions').querySelector('.btn-details');
            const adType = detailsBtn ? detailsBtn.dataset.type : 'blogger';

            // Проверяем наличие всех необходимых данных
            if (!adId || !fromUserId || !toUserId || !adType) {
                console.error('Missing required data:', { adId, fromUserId, toUserId, adType });
                throw new Error('Missing required data for notification');
            }

            const response = await fetch('https://blogy.uz/api/notifications/accept', {
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
                // При успехе показываем галочку
                button.innerHTML = `<div class="success-icon"><i class="fas fa-check"></i></div>`;
                button.style.background = 'linear-gradient(45deg, #32d583, #20bd6d)';
                button.disabled = true;
                Utils.showNotification('Соглашение успешно отправлено', 'success');
            } else {
                // При ошибке показываем крестик с анимацией
                button.innerHTML = `
                    <div class="error-icon">
                        <i class="fas fa-times"></i>
                    </div>
                `;
                button.style.background = 'linear-gradient(45deg, #dc3545, #c82333)';
                button.disabled = false;
                const errorData = await response.json();
                throw new Error(errorData.error || 'Failed to send notification');
            }
        } catch (error) {
            console.error('Error:', error);
            // При ошибке показываем крестик
            button.innerHTML = `
                <div class="error-icon">
                    <i class="fas fa-times"></i>
                </div>
            `;
            button.style.background = 'linear-gradient(45deg, #dc3545, #c82333)';
            button.disabled = false;
            Utils.showNotification(error.message || 'Произошла ошибка', 'error');
        }
    });

    // Обработчик для кнопки "Нет"
    confirmNo.addEventListener('click', () => {
        confirmModal.classList.add('shake');
        setTimeout(() => {
            confirmModal.classList.remove('active', 'shake');
        }, 300);
    });

    // Закрытие при клике вне модального окна
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.confirm-modal') && 
            !e.target.closest('.btn-accept')) {
            confirmModal.classList.remove('active');
        }
    });

    // Добавляем обработчик для всех кнопок accept
    document.addEventListener('click', (e) => {
        const acceptBtn = e.target.closest('.btn-accept');
        if (!acceptBtn) return;

        e.preventDefault();
        e.stopPropagation();
        
        window.showConfirmModal(acceptBtn);
    });
});
  