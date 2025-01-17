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
.input-description {
    color: #4CAF50;
    font-size: 14px;
    margin-bottom: 12px;
    padding: 10px 15px;
    background: rgba(76, 175, 80, 0.1);
    border-left: 3px solid #4CAF50;
    border-radius: 0 8px 8px 0;
    transition: all 0.3s ease;
}

.input-description:hover {
    background: rgba(76, 175, 80, 0.15);
    transform: translateX(5px);
}

.input-description i {
    margin-right: 8px;
    color: #4CAF50;
}

.loading-indicator {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
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

/* Стили для алертов */
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

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Стили для модального окна */
.modal-content {
    background: rgba(33, 37, 41, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 20px;
}

.modal-title {
    color: #fff;
    font-size: 1.5rem;
    font-weight: 500;
}

.modal-body {
    padding: 30px;
}

.modal-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 20px;
}

.success-icon {
    color: #28a745;
    font-size: 64px;
    margin-bottom: 20px;
    animation: scaleIn 0.5s ease-out;
}

.modal-body p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    line-height: 1.5;
    margin-bottom: 10px;
}

.modal .btn-primary {
    background: #0d6efd;
    border: none;
    padding: 12px 30px;
    border-radius: 10px;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.modal .btn-primary:hover {
    background: #0b5ed7;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

/* Анимация появления модального окна */
.modal.fade .modal-dialog {
    transform: scale(0.7);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: scale(1);
}
</style>


<div class="auth-container">
    <form id="addCompanyForm" class="auth-form active">
        <!-- Скрытые поля для direction и telegram_username -->
        <input type="hidden" id="directionInput" name="direction">
        <input type="hidden" id="telegramUsernameInput" name="telegram_username">

        <!-- Загрузка фото -->
        <div class="input-description">
            <i class="fas fa-image"></i>
            Загрузите логотип или фото вашей компании
        </div>
        <div class="form-group mb-4">
            <label class="upload-photo">
                <input type="file" name="photo" accept="image/*" hidden required>
                <div class="upload-placeholder">
                    <i class="fas fa-camera"></i>
                    <span data-translate="addCompany.uploadPhoto">Загрузить фото</span>
                </div>
            </label>
        </div>

        <!-- Название компании -->
        <div class="input-description">
            <i class="fas fa-building"></i>
            Укажите кого вы ищете (Заголовок)
        </div>
        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-building"></i>
                <input type="text" name="name" class="form-control" placeholder="Название компании" required>
            </div>
        </div>

        <!-- Бюджет -->
        <div class="input-description">
            <i class="fas fa-dollar-sign"></i>
            Укажите ваш рекламный бюджет в долларах (если вы хотите на бартер поставьте 0)
        </div>
        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-dollar"></i>
                <input type="number" name="budget" class="form-control" placeholder="Бюджет ($)" required>
            </div>
        </div>

        <!-- Комментарий -->
        <div class="input-description">
            <i class="fas fa-comment"></i>
            Укажите кого вы ищете и о компании подробно (пустой строкой не заполняйте будет отказано)
        </div>
        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-comment"></i>
                <textarea name="ad_comment" class="form-control" rows="4" placeholder="Комментарий к рекламе"></textarea>
            </div>
        </div>

        <!-- Социальные сети -->
        <div class="input-description">
            <i class="fas fa-share-alt"></i>
            Добавьте ссылки на ваши социальные сети и сайт
        </div>

        <!-- Веб-сайт секция -->
        <div class="social-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="social-header">
                    <i class="fas fa-globe"></i>
                    <span>Веб-сайт</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="websiteSwitch">
                </div>
            </div>
            <div class="social-fields" id="websiteFields" style="display: none;">
                <div class="input-with-icon">
                    <i class="fas fa-link"></i>
                    <input type="url" name="website_link" class="form-control" placeholder="Ссылка на сайт">
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
                <div class="input-with-icon">
                    <i class="fas fa-link"></i>
                    <input type="url" name="telegram_link" class="form-control" placeholder="Ссылка на Telegram">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Опубликовать</button>
        
        <!-- Блок для отображения ответа -->
        <div id="apiResponse" class="mt-3" style="display: none;">
            <div class="alert" role="alert"></div>
        </div>

        <!-- Добавьте это скрытое поле внутрь формы -->
        <input type="hidden" name="type" value="ads">
    </form>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="translations.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Инициализация переключателя языка
    const langButtons = document.querySelectorAll('.lang-toggle button');
    const savedLang = localStorage.getItem('selectedLanguage') || 'ru';

    // Устанавливаем активную кнопку языка при загрузке
    langButtons.forEach(button => {
        if (button.textContent.toLowerCase() === savedLang) {
            button.classList.add('active');
        }
    });

    // Обработчики для кнопок языка
    langButtons.forEach(button => {
        button.addEventListener('click', () => {
            const lang = button.textContent.toLowerCase();
            langButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            localStorage.setItem('selectedLanguage', lang);
            updateTranslations(lang);
        });
    });

    // Инициализация пеекючателя темы
    const themeToggle = document.querySelector('.theme-toggle');
    const savedTheme = localStorage.getItem('theme') || 'dark';

    // Устанавливаем тему при загрузке
    if (savedTheme === 'light') {
        document.body.classList.add('light-theme');
        document.querySelector('.fa-sun').style.opacity = '1';
        document.querySelector('.fa-moon').style.opacity = '0.5';
    } else {
        document.querySelector('.fa-sun').style.opacity = '0.5';
        document.querySelector('.fa-moon').style.opacity = '1';
    }

    // Обработчик переключения темы
    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('light-theme');
        const isLight = document.body.classList.contains('light-theme');
        
        // Обновляем иконки
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
                
                if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                    element.placeholder = translation;
                } else {
                    element.textContent = translation;
                }
            } catch (e) {
                console.error('Translation error for key:', key, e);
            }
        });
    }

    // Вызываем перевод при загрузке
    updateTranslations(savedLang);

    // Обработка переключения реклама/бартер
  
    // Обработка загрузки фото
    const photoInput = document.querySelector('input[type="file"]');
    const uploadPlaceholder = document.querySelector('.upload-placeholder');

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
                    const MAX_SIZE = 1000;

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

                    const compressedDataUrl = canvas.toDataURL('image/jpeg', 0.5);
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

    // Добавляем функцию проверки размера
    function checkImageSize(base64String) {
        const sizeInBytes = (base64String.length * 3) / 4;
        const sizeInKB = sizeInBytes / 1024;
        
        if (sizeInKB > 300) {
            const img = new Image();
            img.src = base64String;
            
            return new Promise((resolve) => {
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    let width = img.width;
                    let height = img.height;
                    const MAX_SIZE = 1000;
                    
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
                    
                    let quality = 0.5;
                    let result = canvas.toDataURL('image/jpeg', quality);
                    
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

    // Валидация ссылк
    const urlInputs = document.querySelectorAll('input[type="url"]');
    urlInputs.forEach(input => {
        input.addEventListener('input', () => {
            const url = input.value;
            if (url && !url.startsWith('http://') && !url.startsWith('https://')) {
                input.value = 'https://' + url;
            }
        });
    });

    // Обработка переключателей социальных сетей
    const switches = {
        website: document.getElementById('websiteSwitch'),
        telegram: document.getElementById('telegramSwitch')
    };

    Object.entries(switches).forEach(([platform, switchEl]) => {
        const fields = document.getElementById(`${platform}Fields`);
        const inputs = fields.querySelectorAll('input');

        switchEl.addEventListener('change', () => {
            fields.style.display = switchEl.checked ? 'block' : 'none';
            inputs.forEach(input => {
                input.required = switchEl.checked;
            });
        });
    });

    // Обработчик формы
    const addCompanyForm = document.getElementById('addCompanyForm');
    const responseBlock = document.getElementById('apiResponse');
    const alertBlock = responseBlock.querySelector('.alert');

    if (addCompanyForm) {
        addCompanyForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Предотвращаем стандартную отправку формы
            showLoading();

            try {
                // Получаем сжатое изображение
                const photoBase64 = document.querySelector('input[type="file"]').compressedImage;
                if (!photoBase64) {
                    throw new Error('Пожалуйста, выберите фото');
                }

                // Дополнительная ��птимизация перед отправкой
                const optimizedPhotoBase64 = await checkImageSize(photoBase64);

                // Получаем данные из localStorage
                const userId = localStorage.getItem('userId');
                const category = localStorage.getItem('category');
                const direction = localStorage.getItem('direction');
                const telegramUsername = localStorage.getItem('telegram');

                if (!userId) {
                    throw new Error('Пользователь не авторизован. Пожалуйста, войдите в систему.');
                }

                const postData = {
                    user_id: parseInt(userId),
                    name: addCompanyForm.querySelector('input[placeholder*="Название компании"]').value.trim(),
                    category: category,
                    direction: direction,
                    telegram_username: telegramUsername,
                    photo_base64: optimizedPhotoBase64,
                    budget: parseInt(addCompanyForm.querySelector('input[name="budget"]').value) || 0,
                    ad_comment: addCompanyForm.querySelector('textarea[name="ad_comment"]').value || "",
                    website_link: document.querySelector('#websiteFields input[type="url"]')?.value?.trim() || "",
                    telegram_link: document.querySelector('#telegramFields input[type="url"]')?.value?.trim() || "",
                    instagram_link: localStorage.getItem('instagram') || "",
                };

                // Проверяем данные перед отправкой
                if (!postData.name || !postData.category || !postData.photo_base64) {
                    throw new Error('Пожалуйста, заполните все обязательные поля');
                }

                // Проверяем валидность JSON
                try {
                    JSON.stringify(postData);
                } catch (e) {
                    console.error('Invalid JSON:', e);
                    throw new Error('Ошибка формирования данных');
                }

                console.log('Sending data:', postData);

                const response = await fetch('https://blogy.uz/api/companies', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(postData)
                });

                // После получения ответа
                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Response data:', data);

                responseBlock.style.display = 'block';

                if (response.ok) {
                    // Скрываем предыдущий алерт если он был
                    responseBlock.style.display = 'none';
                    
                    // Показывае�� модальное окно
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    // Добавляем обработчик закрытия модального окна
                    document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
                        window.location.href = 'index.php';
                    });
                } else {
                    throw new Error(data.error || 'Ошибка при создании компании');
                }

            } catch (error) {
                console.error('Error:', error);
                responseBlock.style.display = 'block';
                alertBlock.className = 'alert alert-danger';
                alertBlock.textContent = error.message;
            } finally {
                hideLoading();
            }
        });
    }

    // Функции для управления индикатором загрузки
    function showLoading() {
        const loader = document.getElementById('loadingIndicator');
        if (loader) {
            loader.style.display = 'flex';
            setTimeout(() => loader.classList.add('show'), 10);
        }
    }

    function hideLoading() {
        const loader = document.getElementById('loadingIndicator');
        if (loader) {
            loader.classList.remove('show');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 300);
        }
    }
});
</script>

<!-- В конце файла, перед закрывающим тегом body -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Отлично!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle success-icon"></i>
                <p class="mt-3">Ваше объявление успешно добавлено и отправлено на модерацию.</p>
                <p>Уведомление будет отправлено на вашу почту.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" onclick="window.location.href='index.php'">
                    <i class="fas fa-check"></i> Понятно
                </button>
            </div>
        </div>
    </div>
</div>
</body>
</html> 