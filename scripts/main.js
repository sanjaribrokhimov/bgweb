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

        try {
            const adId = button.dataset.id;
            const fromUserId = localStorage.getItem('userId');
            const toUserId = button.dataset.ownerId;
            const detailsBtn = button.closest('.btn-actions').querySelector('.btn-details');
            const adType = detailsBtn ? detailsBtn.dataset.type : 'blogger';

            console.log('Sending notification:', {
                from_user_id: parseInt(fromUserId),
                to_user_id: parseInt(toUserId),
                ad_id: parseInt(adId),
                ad_type: adType
            });
            
            const response = await fetch('https://bgweb.nurali.uz/api/notifications/accept', {
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
                button.style.background = 'linear-gradient(45deg, #32d583, #20bd6d)';
                button.disabled = true;
                Utils.showNotification('Соглашение успешно отправлено', 'success');
            } else {
                throw new Error('Failed to send notification');
            }
        } catch (error) {
            console.error('Error:', error);
            Utils.showNotification('Произошла ошибка', 'error');
        }

        confirmModal.classList.remove('active');
    });

    // Обработчик для кнопки "Нет"
    confirmNo.addEventListener('click', () => {
        confirmModal.classList.add('shake');
        setTimeout(() => {
            confirmModal.classList.remove('active', 'shake');
        }, 500);
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
  