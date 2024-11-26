class Utils {
    static formatNumber(num) {
        if (!num) return '0';
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    static showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification-toast ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    static async showConfirmModal(button) {
        const rect = button.getBoundingClientRect();
        const confirmModal = document.querySelector('.confirm-modal');
        
        confirmModal.style.top = `${rect.top}px`;
        confirmModal.style.left = `${rect.left}px`;
        confirmModal.classList.add('active');
        confirmModal.currentButton = button;
    }

    static async compressImage(file, maxWidth = 800) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result;
                
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    // Вычисляем новые размеры, сохраняя пропорции
                    let width = img.width;
                    let height = img.height;
                    
                    if (width > maxWidth) {
                        height = (maxWidth * height) / width;
                        width = maxWidth;
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    
                    // Рисуем изображение с новыми размерами
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    // Конвертируем в base64 с качеством 0.8
                    const compressedBase64 = canvas.toDataURL('image/jpeg', 0.8);
                    
                    // Кэшируем изображение
                    Utils.cacheImage(compressedBase64);
                    
                    resolve(compressedBase64);
                };
            };
        });
    }

    // Метод для кэширования изображений
    static cacheImage(imageUrl, cacheName = 'images-cache') {
        if ('caches' in window) {
            caches.open(cacheName).then(cache => {
                // Создаем Response из base64
                const blob = this.base64ToBlob(imageUrl);
                const response = new Response(blob);
                const url = `cached-image-${Date.now()}`;
                cache.put(url, response);
                return url;
            });
        }
    }

    // Конвертация base64 в Blob
    static base64ToBlob(base64) {
        const parts = base64.split(';base64,');
        const contentType = parts[0].split(':')[1];
        const raw = window.atob(parts[1]);
        const rawLength = raw.length;
        const uInt8Array = new Uint8Array(rawLength);

        for (let i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }

        return new Blob([uInt8Array], { type: contentType });
    }

    // Получение изображения из кэша
    static async getImageFromCache(imageUrl, cacheName = 'images-cache') {
        if ('caches' in window) {
            const cache = await caches.open(cacheName);
            const response = await cache.match(imageUrl);
            if (response) {
                return response.blob();
            }
        }
        return null;
    }

    static async handleImageUpload(inputElement, previewElement, maxWidth = 800) {
        const file = inputElement.files[0];
        if (!file) return null;

        try {
            // Показываем индикатор загрузки
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) loadingIndicator.classList.add('active');

            // Сжимаем изображение
            const compressedImage = await Utils.compressImage(file, maxWidth);
            
            // Показываем превью
            if (previewElement) {
                previewElement.src = compressedImage;
                previewElement.style.display = 'block';
            }

            // Кэшируем изображение
            Utils.cacheImage(compressedImage);

            // Скрываем индикатор загрузки
            if (loadingIndicator) loadingIndicator.classList.remove('active');

            return compressedImage;
        } catch (error) {
            console.error('Error processing image:', error);
            Utils.showNotification('Ошибка при обработке изображения', 'error');
            return null;
        }
    }
} 