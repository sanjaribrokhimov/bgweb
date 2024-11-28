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
</style>


<div class="auth-container">
    <form id="addCompanyForm" class="auth-form active">
        <!-- Загрузка фото -->
        <div class="form-group mb-4">
            <label class="upload-photo">
                <input type="file" name="photo" accept="image/*" hidden required>
                <div class="upload-placeholder">
                    <i class="fas fa-camera"></i>
                    <span data-translate="addCompany.uploadPhoto">Загрузить фото</span>
                </div>
            </label>
        </div>

        <!-- Основная информация -->
        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-building"></i>
                <input type="text" name="name" class="form-control" placeholder="Название компании" required>
            </div>
        </div>

        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-tag"></i>
                <select class="form-control category-select" required>
                    <option value="" disabled selected>Выберите категорию</option>
                    <option value="retail">Retail & E-commerce</option>
                    <option value="tech">Technology & Software</option>
                    <option value="finance">Finance & Banking</option>
                    <option value="healthcare">Healthcare & Medical</option>
                    <option value="education">Education & E-learning</option>
                    <option value="food">Food & Restaurant</option>
                    <option value="fashion">Fashion & Apparel</option>
                    <option value="beauty">Beauty & Cosmetics</option>
                    <option value="automotive">Automotive</option>
                    <option value="real-estate">Real Estate</option>
                    <option value="travel">Travel & Tourism</option>
                    <option value="sports">Sports & Fitness</option>
                    <option value="entertainment">Entertainment & Media</option>
                    <option value="telecom">Telecommunications</option>
                    <option value="manufacturing">Manufacturing</option>
                    <option value="construction">Construction</option>
                    <option value="agriculture">Agriculture</option>
                    <option value="energy">Energy & Utilities</option>
                    <option value="logistics">Logistics & Transportation</option>
                    <option value="consulting">Consulting & Professional Services</option>
                </select>
            </div>
        </div>

        <!-- Поля для рекламы -->
        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-dollar"></i>
                <input type="number" name="budget" class="form-control" placeholder="Бюджет ($)" required>
            </div>
        </div>



        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-comment"></i>
                <textarea name="ad_comment" class="form-control" rows="4" placeholder="Комментарий к рекламе"></textarea>
            </div>
        </div>

        <!-- Социальные сети -->
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
                <div class="input-with-icon">
                    <i class="fas fa-link"></i>
                    <input type="url" name="instagram_link" class="form-control" placeholder="Ссылка на Instagram">
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

    // Инициализация пееключателя темы
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

    // Валидация ссылок
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
        instagram: document.getElementById('instagramSwitch'),
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
    if (addCompanyForm) {
        addCompanyForm.addEventListener('submit', async (e) => {
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

                // Дополнительная оптимизация перед отправкой
                const optimizedPhotoBase64 = await checkImageSize(photoBase64);

                // Получаем ID пользователя из localStorage
                const userId = localStorage.getItem('userId');
                if (!userId) {
                    throw new Error('Пользователь не авторизован. Пожалуйста, войдите в систему.');
                }

                const postData = {
                    user_id: parseInt(userId),
                    name: addCompanyForm.querySelector('input[placeholder*="Название компании"]').value.trim(),
                    category: addCompanyForm.querySelector('.category-select').value,
                    photo_base64: optimizedPhotoBase64, // Используем оптимизированное изображение
                    budget: parseInt(addCompanyForm.querySelector('input[name="budget"]').value) || 0,
                    ad_comment: addCompanyForm.querySelector('textarea[name="ad_comment"]').value || "",
                    website_link: document.querySelector('#websiteFields input[type="url"]')?.value?.trim() || "",
                    instagram_link: document.querySelector('#instagramFields input[type="url"]')?.value?.trim() || "",
                    telegram_link: document.querySelector('#telegramFields input[type="url"]')?.value?.trim() || ""
                };

                console.log('Sending data:', postData);

                // Перед отправкой данных
                if (!postData.name || !postData.category || !postData.photo_base64) {
                    throw new Error('Пожалуйста, заполните все обязательные поля');
                }

                console.log('Sending data:', postData);

                // Отправляем данные на API
                const response = await fetch('https://bgweb.nurali.uz/api/companies', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(postData)
                });

                // Добавим проверку статуса
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Ошибка при создании компании');
                }

                const data = await response.json();
                console.log('Response:', data);
                
                responseBlock.style.display = 'block';

                if (response.ok) {
                    alertBlock.className = 'alert alert-success';
                    alertBlock.textContent = data.message || 'Компания успешно создана';
                    
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Ошибка при создании компании');
                }

            } catch (error) {
                console.error('Error:', error);
                responseBlock.style.display = 'block';
                alertBlock.className = 'alert alert-danger';
                if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                    alertBlock.textContent = 'Ошибка соединения с сервером. Пожалуйста, проверьте подключение к интернету.';
                } else {
                    alertBlock.textContent = error.message;
                }
            } finally {
                hideLoading();
            }
        });
    }

    // Добавьте функции для управления индикатором загрузки
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
});
</script>
</body>
</html> 