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
    <form id="addFreelancerForm" class="auth-form active">
        <!-- Загрузка фото -->
        <div class="form-group mb-4">
            <label class="upload-photo">
                <input type="file" accept="image/*" hidden required>
                <div class="upload-placeholder">
                    <i class="fas fa-camera"></i>
                    <span data-translate="addFreelancer.uploadPhoto">Загрузить фото</span>
                </div>
            </label>
        </div>

        <!-- Основная информация -->
        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" placeholder="Имя" data-translate="addFreelancer.name" required>
            </div>
        </div>

        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-tag"></i>
                <select class="form-control category-select" required>
                <option value="all" data-translate="categories.all">All Categories</option>
                        <option value="web">Web Development</option>
                        <option value="mobile">Mobile Development</option>
                        <option value="design">UI/UX Design</option>
                        <option value="graphics">Graphic Design</option>
                        <option value="video">Video Editing</option>
                        <option value="marketing">Digital Marketing</option>
                        <option value="seo">SEO</option>
                        <option value="content">Content Writing</option>
                        <option value="translation">Translation</option>
                        <option value="smm">SMM</option>
                        <option value="photo">Photography</option>
                        <option value="audio">Audio Production</option>
                        <option value="animation">Animation</option>
                        <option value="3d">3D Modeling</option>
                        <option value="data">Data Analysis</option>
                </select>
            </div>
        </div>



        

        <!-- Условия бартера / Комментарий к рекламе -->
        <div class="form-group mb-4 conditions-field">
            <div class="input-with-icon">
                <i class="fas fa-handshake barter-icon"></i>
                <i class="fas fa-comment ad-icon" style="display: none;"></i>
                <textarea class="form-control" rows="4" placeholder="Комментарий" data-translate-barter="addFreelancer.barterConditions" data-translate-ad="addFreelancer.adComment"></textarea>
            </div>
        </div>

        <!-- GitHub секция -->
        <div class="social-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="social-header">
                    <i class="fab fa-github"></i>
                    <span>GitHub</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="githubSwitch">
                </div>
            </div>
            <div class="social-fields" id="githubFields" style="display: none;">
                <div class="input-with-icon mb-2">
                    <i class="fas fa-link"></i>
                    <input type="url" class="form-control" placeholder="Ссылка на GitHub" data-translate="addFreelancer.github">
                </div>
            </div>
        </div>

        <!-- Portfolio секция -->
        <div class="social-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="social-header">
                    <i class="fas fa-briefcase"></i>
                    <span>Portfolio</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="portfolioSwitch">
                </div>
            </div>
            <div class="social-fields" id="portfolioFields" style="display: none;">
                <div class="input-with-icon mb-2">
                    <i class="fas fa-link"></i>
                    <input type="url" class="form-control" placeholder="Ссылка на портфолио" data-translate="addFreelancer.portfolio">
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
                <div class="input-with-icon mb-2">
                    <i class="fas fa-link"></i>
                    <input type="url" class="form-control" placeholder="Ссылка на Instagram" data-translate="addFreelancer.instagram">
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
                    <input type="url" class="form-control" placeholder="Ссылка на Telegram" data-translate="addFreelancer.telegram">
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
                    <input type="url" class="form-control" placeholder="Ссылка на YouTube" data-translate="addFreelancer.youtube">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100" data-translate="addFreelancer.publish">Опубликовать</button>

        <!-- Блок для отображения ответа -->
        <div id="apiResponse" class="mt-3" style="display: none;">
            <div class="alert" role="alert"></div>
        </div>
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

    // Инициализация переключателя темы
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
        
        document.querySelector('.fa-sun').style.opacity = isLight ? '1' : '0.5';
        document.querySelector('.fa-moon').style.opacity = isLight ? '0.5' : '1';
        
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
    const postTypeInputs = document.querySelectorAll('input[name="postType"]');
    const conditionsField = document.querySelector('.conditions-field');
    const textarea = conditionsField.querySelector('textarea');
    const barterIcon = conditionsField.querySelector('.barter-icon');
    const adIcon = conditionsField.querySelector('.ad-icon');

    // Устанавливаем начальное состояние (реклама)
    textarea.placeholder = translations[savedLang].addFreelancer.adComment;
    barterIcon.style.display = 'none';
    adIcon.style.display = 'block';

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

    // Обработка переключателей социальных сетей
    const switches = {
        github: document.getElementById('githubSwitch'),
        portfolio: document.getElementById('portfolioSwitch'),
        instagram: document.getElementById('instagramSwitch'),
        telegram: document.getElementById('telegramSwitch'),
        youtube: document.getElementById('youtubeSwitch')
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
    const addFreelancerForm = document.getElementById('addFreelancerForm');
    if (addFreelancerForm) {
        addFreelancerForm.addEventListener('submit', async (e) => {
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
                    name: addFreelancerForm.querySelector('input[placeholder*="Имя"]').value.trim(),
                    category: addFreelancerForm.querySelector('.category-select').value,
                    photo_base64: optimizedPhotoBase64,
                    ad_comment: addFreelancerForm.querySelector('textarea').value || "",
                    github_link: document.querySelector('#githubFields input[type="url"]')?.value?.trim() || "",
                    portfolio_link: document.querySelector('#portfolioFields input[type="url"]')?.value?.trim() || "",
                    instagram_link: document.querySelector('#instagramFields input[type="url"]')?.value?.trim() || "",
                    telegram_link: document.querySelector('#telegramFields input[type="url"]')?.value?.trim() || "",
                    youtube_link: document.querySelector('#youtubeFields input[type="url"]')?.value?.trim() || ""
                };

                console.log('Sending data:', postData);

                // Перед отправкой данных
                if (!postData.name || !postData.category || !postData.photo_base64) {
                    throw new Error('Пожалуйста, заполните все обязательные поля');
                }

                // Отправляем данные на API
                const response = await fetch('https://bgweb.nurali.uz/api/freelancers', {
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
                    throw new Error(errorData.message || 'Ошибка при создании профиля фрилансера');
                }

                const data = await response.json();
                console.log('Response:', data);
                
                responseBlock.style.display = 'block';

                if (response.ok) {
                    alertBlock.className = 'alert alert-success';
                    alertBlock.textContent = data.message || 'Профиль фрилансера успешно создан';
                    
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Ошибка при создании профиля фрилансера');
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

    // Функция управления индикатором загрузки
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