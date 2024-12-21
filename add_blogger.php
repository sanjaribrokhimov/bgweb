<?php include 'components/miniHeader.php'; ?>
<!-- Индикатор загрузки -->
<div id="loadingIndicator" class="loading-indicator">
    <div class="spinner-wrapper">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="loading-text">Пожалуйста, подождите...</div>
    </div>
</div>

<style>
.loading-indicator {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.alert {
    margin-top: 15px;
    border-radius: 8px;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.loading-indicator {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.loading-indicator.show {
    opacity: 1;
}

.spinner-wrapper {
    text-align: center;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
    color: var(--accent-blue);
}

.loading-text {
    color: #fff;
    margin-top: 1rem;
    font-size: 14px;
}

.alert {
    padding: 12px;
    border-radius: 8px;
    margin-top: 15px;
    animation: fadeIn 0.3s;
}

.alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
}

.alert-success {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
}
.form-control{
    color:white;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="auth-container">
    <form id="addBloggerForm" class="auth-form active">
        <!-- Загрузка фото -->
        <div class="form-group mb-4">
            <label class="upload-photo">
                <input type="file" name="photo" accept="image/*" hidden required>
                <div class="upload-placeholder">
                    <i class="fas fa-camera"></i>
                    <span data-translate="addBlogger.uploadPhoto">Загрузить фото</span>
                </div>
            </label>
        </div>

        <!-- Основная информация -->
        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-user"></i>
                <input type="text" name="title" class="form-control" placeholder="Заголовок" required>
            </div>
        </div>

        <!-- Условия бартера / Комментарий к рекламе -->
        <div class="form-group mb-4 conditions-field">
            <h4>Кого вы ищете или условия бартера</h4>
            <div class="input-with-icon">
                <i class="fas fa-handshake barter-icon"></i>
                <textarea  class="form-control" rows="4"  required></textarea>
            </div>
        </div>
        <h4>Ваши социальные сети</h4>
        <!-- Instagram секция -->
        <div class="social-section mb-4">
           
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="social-header">
                    <i class="fab fa-instagram"></i>
                    <span>Instagram</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="instagramSwitch">
                </div>
            </div>
            <div class="social-fields" id="instagramFields" style="display: none;">
                <div class="input-with-icon mb-2">
                    <i class="fas fa-link"></i>
                    <input type="url" name="instagram_link" class="form-control" placeholder="Ссылка на Instagram" data-translate="addBlogger.instagram.link">
                </div>
                
                <div class="form-group mb-3 comment-field" style="display: none;">
                    <div class="input-with-icon">
                        <i class="fas fa-comment"></i>
                        <textarea class="form-control" rows="4" placeholder="Комментарий к рекламе" data-translate="addBlogger.adComment"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Telegram секция -->
        <div class="social-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="social-header">
                    <i class="fab fa-telegram"></i>
                    <span>Telegram</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="telegramSwitch">
                </div>
            </div>
            <div class="social-fields" id="telegramFields" style="display: none;">
                <div class="input-with-icon mb-2">
                    <i class="fas fa-link"></i>
                    <input type="url" name="telegram_link" class="form-control" placeholder="Ссылка на Telegram" data-translate="addBlogger.telegram.link">
                </div>
               
            </div>
        </div>

        <!-- YouTube секция -->
        <div class="social-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="social-header">
                    <i class="fab fa-youtube"></i>
                    <span>YouTube</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="youtubeSwitch">
                </div>
            </div>
            <div class="social-fields" id="youtubeFields" style="display: none;">
                <div class="input-with-icon mb-2">
                    <i class="fas fa-link"></i>
                    <input type="url" name="youtube_link" class="form-control" placeholder="Ссылка на канал" data-translate="addBlogger.youtube.link">
                </div>
                
            </div>
        </div>



        <button type="submit" class="btn btn-primary w-100" data-translate="addBlogger.publish">Опубликовать</button>
        
        <!-- Блок для отображения ответа -->
        <div id="apiResponse" class="mt-3" style="display: none;">
            <div class="alert" role="alert"></div>
        </div>
    </form>
</div>

    <!-- Подключаем скрипты -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="translations.js"></script>
    <script>
    // Добавьте функции для управления индикатором загрузки в начало скрипта
    function showLoading() {
        const loader = document.getElementById('loadingIndicator');
        loader.style.display = 'flex';
        setTimeout(() => loader.classList.add('show'), 10);
    }

    function hideLoading() {
        const loader = document.getElementById('loadingIndicator');
        loader.classList.remove('show');
        setTimeout(() => {
            loader.style.display = 'none';
        }, 300);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Обработка переключателей социальных сетей
        const switches = {
            instagram: document.getElementById('instagramSwitch'),
            telegram: document.getElementById('telegramSwitch'),
            youtube: document.getElementById('youtubeSwitch'),
         
        };

        Object.entries(switches).forEach(([platform, switchEl]) => {
            const fields = document.getElementById(`${platform}Fields`);
            
            switchEl.addEventListener('change', () => {
                const isChecked = switchEl.checked;
                fields.style.display = isChecked ? 'block' : 'none';
                
                // URL поле
                const urlInput = fields.querySelector('input[type="url"]');
                if (urlInput) {
                    urlInput.required = isChecked;
                }
            });
        });

        // Инициализация переключателя языка
        const langButtons = document.querySelectorAll('.lang-toggle button');
        const savedLang = localStorage.getItem('selectedLanguage') || 'ru';

        // Устнавливаем активную кнопку языка при загрузке
        langButtons.forEach(button => {
            if (button.textContent.toLowerCase() === savedLang) {
                button.classList.add('active');
            } else {
                button.classList.remove('active');
            }
        });

        // Обработчики для кнопк язык
        langButtons.forEach(button => {
            button.addEventListener('click', () => {
                const lang = button.textContent.toLowerCase();
                langButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                localStorage.setItem('selectedLanguage', lang);
                updateTranslations(lang);
            });
        });

        // ициализация темы
        const themeToggle = document.querySelector('.theme-toggle');
        const savedTheme = localStorage.getItem('theme') || 'dark';

        // Устаналиваем тему при загрузке
        if (savedTheme === 'light') {
            document.body.classList.add('light-theme');
            document.querySelector('.fa-sun').style.opacity = '1';
            document.querySelector('.fa-moon').style.opacity = '0.5';
        } else {
            document.querySelector('.fa-sun').style.opacity = '0.5';
            document.querySelector('.fa-moon').style.opacity = '1';
        }

        // Обработчик переклюения темы
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('light-theme');
            const isLight = document.body.classList.contains('light-theme');
            
            // Оновляем иконки
            document.querySelector('.fa-sun').style.opacity = isLight ? '1' : '0.5';
            document.querySelector('.fa-moon').style.opacity = isLight ? '0.5' : '1';
            
            // Сохраняем выбор
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
        });

        // Функция обновления переводов
        function updateTranslations(lang) {
            document.querySelectorAll('[data-translate]').forEach(element => {
                const key = element.getAttribute('data-translate');
                try {
                    const keys = key.split('.');
                    let translation = translations[lang];
                    for (let k of keys) {
                        translation = translation[k];
                    }
                    
                    // Обработка разных типов элементов
                    if (element.tagName === 'INPUT') {
                        // Для инпутов обновляем placeholder
                        element.placeholder = translation;
                    } else if (element.tagName === 'TEXTAREA') {
                        // Для textarea обновляем placeholder
                        element.placeholder = translation;
                    } else if (element.tagName === 'SELECT') {
                        // Для select обновляем placeholder у option с disabled
                        if (element.firstElementChild && element.firstElementChild.disabled) {
                            element.firstElementChild.textContent = translation;
                        }
                    } else if (element.tagName === 'OPTION') {
                        // Для option обновляем текст
                        element.textContent = translation;
                    } else {
                        // Для остальных элеентов обновляем текст
                        element.textContent = translation;
                    }
                } catch (e) {
                    console.error('Translation error for key:', key, e);
                }
            });

            // Дополнительно проверяем и обновляем все placeholder'ы
            const placeholderElements = {
                'addBlogger.nickname': 'input[data-translate="addBlogger.nickname"]',
                'addBlogger.followers': 'input[data-translate="addBlogger.followers"]',
                'addBlogger.engagement': 'input[data-translate="addBlogger.engagement"]',
                'addBlogger.telegramUsername': 'input[data-translate="addBlogger.telegramUsername"]',
                'addBlogger.instagram.link': 'input[data-translate="addBlogger.instagram.link"]',
                'addBlogger.instagram.postPrice': 'input[data-translate="addBlogger.instagram.postPrice"]',
                'addBlogger.instagram.storyPrice': 'input[data-translate="addBlogger.instagram.storyPrice"]',
                'addBlogger.instagram.photoPrice': 'input[data-translate="addBlogger.instagram.photoPrice"]',
                'addBlogger.telegram.link': 'input[data-translate="addBlogger.telegram.link"]',
                'addBlogger.telegram.postPrice': 'input[data-translate="addBlogger.telegram.postPrice"]',
                'addBlogger.youtube.link': 'input[data-translate="addBlogger.youtube.link"]',
                'addBlogger.youtube.adPrice': 'input[data-translate="addBlogger.youtube.adPrice"]',
                'addBlogger.barterConditions': 'textarea[data-translate="addBlogger.barterConditions"]'
            };

            // Применяем переводы к каждому элементу
            Object.entries(placeholderElements).forEach(([translationKey, selector]) => {
                const element = document.querySelector(selector);
                if (element) {
                    const keys = translationKey.split('.');
                    let translation = translations[lang];
                    for (let key of keys) {
                        translation = translation[key];
                    }
                    element.placeholder = translation;
                }
            });
        }

        // Вызываем перевод при загрузке
        updateTranslations(savedLang);

        // Обработка загрузки фото
        const photoInput = document.querySelector('input[type="file"]');
        const uploadPlaceholder = document.querySelector('.upload-placeholder');

        // Функция проверки и оптимизации размера изображения
        function checkImageSize(base64String) {
            // Примерный размер в байтах
            const sizeInBytes = (base64String.length * 3) / 4;
            const sizeInKB = sizeInBytes / 1024;
            
            if (sizeInKB > 300) { // Изменено с 500 на 300
                const img = new Image();
                img.src = base64String;
                
                return new Promise((resolve) => {
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        
                        // Уменьшаем размеры изображения, если оно слишком большое
                        let width = img.width;
                        let height = img.height;
                        const MAX_SIZE = 1000; // Уменьшено с 1200 на 1000
                        
                        if (width > height && width > MAX_SIZE) {
                            height *= MAX_SIZE / width;
                            width = MAX_SIZE;
                        } else if (height > MAX_SIZE) {
                            width *= MAX_SIZE / height;
                            height = MAX_SIZE;
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Начинаем с более низкого качества
                        let quality = 0.5; // Изменено с 0.6 на 0.5
                        let result = canvas.toDataURL('image/jpeg', quality);
                        
                        // Уменьшаем качество, пока размер не станет меньше 300KB
                        while ((result.length * 3) / 4 / 1024 > 300 && quality > 0.1) {
                            quality -= 0.1;
                            result = canvas.toDataURL('image/jpeg', quality);
                        }
                        
                        resolve(result);
                    };
                });
            }
            
            return Promise.resolve(base64String);
        }

        // Обновляе обработчик загрузки фото
        photoInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (file) {
                const img = new Image();
                const reader = new FileReader();
                
                reader.onload = async (e) => {
                    img.src = e.target.result;
                    img.onload = async () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');

                        let width = img.width;
                        let height = img.height;
                        const MAX_SIZE = 1000; // Уменьшено с 1200 на 1000

                        if (width > height && width > MAX_SIZE) {
                            height *= MAX_SIZE / width;
                            width = MAX_SIZE;
                        } else if (height > MAX_SIZE) {
                            width *= MAX_SIZE / height;
                            height = MAX_SIZE;
                        }

                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);

                        // Начинаем с качества 0.5
                        const compressedDataUrl = canvas.toDataURL('image/jpeg', 0.5);
                        
                        // Проверяем и оптимизируем размер
                        const optimizedDataUrl = await checkImageSize(compressedDataUrl);

                        uploadPlaceholder.innerHTML = `
                            <img src="${optimizedDataUrl}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                        `;

                        photoInput.compressedImage = optimizedDataUrl;
                    };
                };
                reader.readAsDataURL(file);
            }
        });

        // Обработчик формы
        const addBloggerForm = document.getElementById('addBloggerForm');
        if (addBloggerForm) {
            addBloggerForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const loadingIndicator = document.getElementById('loadingIndicator');
                const responseBlock = document.getElementById('apiResponse');
                const alertBlock = responseBlock.querySelector('.alert');
                
                try {
                    showLoading();
                    
                    // Получаем сжатое изображение
                    const photoBase64 = document.querySelector('input[type="file"]').compressedImage;
                    if (!photoBase64) {
                        throw new Error('Пожалуйста, выберите фото');
                    }

                    // Используйте эту функцию в обработчике формы перед отправкой
                    const optimizedPhotoBase64 = await checkImageSize(photoBase64);

                    // Получаем ID ползователя из localStorage
                    const userId = localStorage.getItem('userId');
                    if (!userId) {
                        throw new Error('Пользователь не авторизован');
                    }

                    // Собираем данные из формы, используя безопасные проверки
                    const postData = {
                        user_id: parseInt(userId),
                        name: addBloggerForm.querySelector('input[name="title"]').value.trim(),
                        photo_base64: optimizedPhotoBase64,
                        looking_for: addBloggerForm.querySelector('textarea').value || "",
                        category: localStorage.getItem('category') || "",
                        direction: localStorage.getItem('direction') || "",
                        telegram_username: localStorage.getItem('telegram') || "",
                        instagram_link: document.querySelector('#instagramFields input[type="url"]')?.value?.trim() || "",
                        telegram_link: document.querySelector('#telegramFields input[type="url"]')?.value?.trim() || "",
                        youtube_link: document.querySelector('#youtubeFields input[type="url"]')?.value?.trim() || ""
                    };

                    // Перед отправкой данных
                    if (!postData.name || !postData.photo_base64 || !postData.looking_for || !postData.category || !postData.telegram_username) {
                        throw new Error('Пожалуйста, заполните все обязательные поля');
                    }

                    console.log('Отправляемые данные:', postData);

                    // Отправляем данные на API
                    const response = await fetch('http://localhost:8888/api/post-bloggers', {
                        method: 'POST',
                        credentials: 'include',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(postData)
                    });

                    const data = await response.json();
                    
                    responseBlock.style.display = 'block';

                    if (response.ok) {
                        alertBlock.className = 'alert alert-success';
                        alertBlock.textContent = data.message || 'Пост успешно создан';
                        
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Ошибка при создании поста');
                    }

                } catch (error) {
                    console.error('Детали ошибки:', error);
                    responseBlock.style.display = 'block';
                    alertBlock.className = 'alert alert-danger';
                    alertBlock.textContent = error.message || 'Произошла неизвестная ошибка';
                } finally {
                    hideLoading();
                }
            });
        }
    });
</script>
</body>
</html> 