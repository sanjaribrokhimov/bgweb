const freeLancerModal = `
<div class="auth-container">
    <form id="addFreelancerForm" class="auth-form active addSomethingForm">
        <!-- Загрузка фото -->
        <div class="input-description">
            <i class="fas fa-image"></i>
            Загрузите вашу фотографию или логотип
        </div>
        <div class="form-group mb-4">
            <label class="upload-photo">
                <input type="file" accept="image/*" hidden required>
                <div class="upload-placeholder">
                    <i class="fas fa-camera"></i>
                    <span data-translate="addFreelancer.uploadPhoto">Загрузить фото</span>
                </div>
            </label>
        </div>

        <!-- Имя -->
        <div class="input-description">
            <i class="fas fa-user"></i>
            Укажите кто вы кого ищете (Заголовок)
        </div>
        <div class="form-group mb-3">
            <div class="input-with-icon">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" placeholder="Имя" data-translate="addFreelancer.name" required>
            </div>
        </div>

        <!-- Комментарий -->
        <div class="input-description">
            <i class="fas fa-comment-dots"></i>
            Расскажите о своих навыках и опыте работы и о том кого вы ищете (пустой строкой не заполняйте будет отказано)
        </div>
        <div class="form-group mb-4 conditions-field">
            <div class="input-with-icon">
                <i class="fas fa-handshake barter-icon"></i>
                <i class="fas fa-comment ad-icon" style="display: none;"></i>
                <textarea class="form-control" rows="4" placeholder="Комментарий" data-translate-barter="addFreelancer.barterConditions" data-translate-ad="addFreelancer.adComment"></textarea>
            </div>
        </div>

        <!-- Социальные сети -->
        <div class="input-description">
            <i class="fas fa-share-alt"></i>
            Добавьте ссылки на ваши проекты и социальные сети
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
`

const companyModal = `
<div class="auth-container">
    <form id="addCompanyForm" class="auth-form active addSomethingForm">
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
`

const bloggerModal = `
<div class="auth-container">
    <form id="addBloggerForm" class="auth-form active addSomethingForm">
        <!-- Загрузка фото -->
         <div class="input-description">
            <i class="fas fa-image"></i>
            Пожалуйста, загрузите фотографию вашего профиля
        </div>
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
        <div class="input-description">
            <i class="fas fa-user"></i>
            Укажите кого вы ищете (Заголовок)
        </div>
        <div class="form-group mb-3">
            <div class="input-with-icon">
                <input type="text" name="title" class="form-control" placeholder="Заголовок" required>
            </div>
        </div>

        <!-- Условия бартера / Комментарий к рекламе -->
        <div class="input-description">
            <i class="fas fa-comment-dots"></i>
            Укажите кого вы ищете подробно (пустой строкой не заполняйте будет отказано)
        </div>
        <div class="form-group mb-4 conditions-field">
            <div class="input-with-icon">
                <i class="fas fa-handshake barter-icon"></i>
                <textarea  class="form-control" rows="4"  required></textarea>
            </div>
        </div>
        <div class="input-description">
            <i class="fas fa-share-alt"></i>
            Укажите ваши социальные сети (если есть)
        </div>
        <!-- Instagram секция -->
        

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
`

function getFormByType(type) {
    if (type === 'freelancer') {
        return freeLancerModal;
    } else if (type === 'company') {
        return companyModal;
    } else if (type === 'blogger') {
        return bloggerModal;
    }
}



function domContentOnMinimalize(userId, userType, user_direction, userTelegram, userInstagram){

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

    if(userType === 'freelancer'){
        // Обработка переключателей социальных сетей
        const switches = {
            github: document.getElementById('githubSwitch'),
            portfolio: document.getElementById('portfolioSwitch'),

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
    }else if(userType === 'company'){
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
    }
    else if(userType === 'blogger'){
        // Обработка переключателей социальных сетей
        const switches = {
            
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
    }
    

    // Обработчик формы
    // const addFreelancerForm = document.getElementById('addFreelancerForm');
    // const addCompanyForm = document.getElementById('addCompanyForm');
    // const addBloggerForm = document.getElementById('addBloggerForm');
    const postForm = document.querySelector('.addSomethingForm');
    if (postForm) {
        postForm.addEventListener('submit', async (e) => {
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
                const direction = localStorage.getItem('direction');
                if (!userId) {
                    throw new Error('Пользователь не авторизован. Пожалуйста, войдите в систему.');
                }
                var postUrl = '';
                if(userType === 'freelancer'){
                    postUrl = 'http://localhost:8888/api/freelancers';
                    var postData = {
                        user_id: parseInt(userId),
                        name: postForm.querySelector('input[placeholder*="Имя"]').value.trim(),
                        category: userType,
                        photo_base64: optimizedPhotoBase64,
                        ad_comment: postForm.querySelector('textarea').value || "",
                        github_link: document.querySelector('#githubFields input[type="url"]')?.value?.trim() || "",
                        portfolio_link: document.querySelector('#portfolioFields input[type="url"]')?.value?.trim() || "",
                        telegram_link: document.querySelector('#telegramFields input[type="url"]')?.value?.trim() || "",
                        instagram_link: userInstagram || "",
                        youtube_link: document.querySelector('#youtubeFields input[type="url"]')?.value?.trim() || ""
                    };
                }else if(userType === 'company'){
                    postUrl = 'http://localhost:8888/api/companies';
                    var postData = {
                        user_id: parseInt(userId),
                        name: postForm.querySelector('input[placeholder*="Название компании"]').value.trim(),
                        category: userType,
                        direction: user_direction,
                        telegram_username: userTelegram || "",
                        photo_base64: optimizedPhotoBase64,
                        budget: parseInt(postForm.querySelector('input[name="budget"]').value) || 0,
                        ad_comment: postForm.querySelector('textarea[name="ad_comment"]').value || "",
                        website_link: document.querySelector('#websiteFields input[type="url"]')?.value?.trim() || "",
                        telegram_link: document.querySelector('#telegramFields input[type="url"]')?.value?.trim() || "",
                        instagram_link: localStorage.getItem('instagram') || "",
                    };
                }else if(userType === 'blogger'){
                    postUrl = 'http://localhost:8888/api/post-bloggers';
                    var postData = {
                        user_id: parseInt(userId),
                        name: postForm.querySelector('input[name="title"]').value.trim(),
                        photo_base64: optimizedPhotoBase64,
                        looking_for: postForm.querySelector('textarea').value || "",
                        category: userType,
                        direction: user_direction,
                        telegram_username: userTelegram || "",
                        telegram_link: document.querySelector('#telegramFields input[type="url"]')?.value?.trim() || "",
                        youtube_link: document.querySelector('#youtubeFields input[type="url"]')?.value?.trim() || "",
                        instagram_link: userInstagram || ""
                    };
                }
                

                console.log('Sending data:', postData);

                // Перед отправкой данных
                if (!postData.name || !postData.category || !postData.photo_base64) {
                    throw new Error('Пожалуйста, заполните все обязательные поля');
                }

                // Отправляем данные на API
                const response = await fetch(`${postUrl}`, {
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
                    // Скрываем предыдущий алерт если он был
                    responseBlock.style.display = 'none';
                    
                    // Показываем модальное окно
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    // Добавляем обработчик закрытия модального окна
                    document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
                        window.location.href = 'index.php';
                    });
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
}