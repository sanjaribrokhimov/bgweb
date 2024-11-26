<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BGA - login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Добавьте в конец существующих стилей */

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

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.2);
        }

        /* Анимация появления */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Стили для невалидных полей */
        .form-control.is-invalid {
            border-color: var(--danger-color, #dc3545);
            animation: shake 0.5s;
        }

        /* Анимация встряхивания для ошибок */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Стили для индикатора загрузки */
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
    </style>
</head>
<body>
   <!-- Индикатор загрузки -->
<div id="loadingIndicator" class="loading-indicator" style="display: none;">
    <div class="spinner-wrapper">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="loading-text">Пожалуйста, подождите...</div>
    </div>
</div>

    <div class="container-fluid p-3 app-container">
        <!-- Верхняя панель с переключателями -->
        <div class="top-bar d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="language-switcher">
                    <div class="lang-toggle">
                        <button class="active">RU</button>
                        <button>UZ</button>
                    </div>
                </div>
                <div class="theme-switcher">
                    <div class="theme-toggle" role="button" aria-label="Переключить тему">
                        <i class="fas fa-sun"></i>
                        <i class="fas fa-moon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Логотип -->
        <div class="text-center mb-4">
            <img src="img/bga.png" alt="BGA" class="header-logo">
        </div>

        <!-- Форма входа/регистрации -->
        <div class="auth-container">
            <!-- Переключатель форм -->
            <div class="auth-toggle mb-4">
                <button class="active" data-form="login">Вход</button>
                <button data-form="register">Регистрация</button>
            </div>

            <!-- Форма входа -->
            <form id="loginForm" class="auth-form active" autocomplete="off">
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="form-control" placeholder="Email" required autocomplete="off">
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" placeholder="Пароль" required autocomplete="new-password">
                        <button type="button" class="password-toggle">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                    </div>
                    <a href="#" class="forgot-password">Забыли пароль?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
                
                <!-- Изменяем id для блока ответа -->
                <div id="loginApiResponse" class="mt-3" style="display: none;">
                    <div class="alert" role="alert"></div>
                </div>
            </form>

            <!-- Форма регистрации -->
            <form id="registerForm" class="auth-form" autocomplete="off">
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" placeholder="Имя" required autocomplete="off">
                    </div>
                </div>

                <!-- Добавляем поле для номера телефона -->
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="tel" class="form-control" placeholder="Номер телефона" required autocomplete="off" 
                               pattern="[\+]?[0-9]{12}" title="Формат: +998 XX XXX XX XX">
                    </div>
                </div>

                <!-- Селектор категории -->
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-tag"></i>
                        <select class="form-control category-select" required autocomplete="off">
                            <option value="" disabled selected data-translate="auth.selectCategory">Выберите категорию</option>
                            <option value="blogger" data-translate="auth.categories.blogger">Блогер</option>
                            <option value="company" data-translate="auth.categories.company">Компания</option>
                            <option value="freelancer" data-translate="auth.categories.freelancer">Фрилансер</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="form-control" placeholder="Email" required autocomplete="off">
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" placeholder="Пароь" required autocomplete="new-password">
                        <button type="button" class="password-toggle">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" placeholder="Подтвердите пароль" required autocomplete="new-password">
                        <button type="button" class="password-toggle">
                            <i class="far fa-eye"></i>
                        </button>
                        <div class="invalid-feedback">
                            Пароли не совпадают
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                
                <!-- Изменяем id для блока ответа -->
                <div id="registerApiResponse" class="mt-3" style="display: none;">
                    <div class="alert" role="alert"></div>
                </div>
            </form>

            <!-- Социальные сети -->
            <div class="social-auth mt-4">
                <p class="text-center mb-3">Или во через</p>
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-outline-primary social-btn">
                        <i class="fab fa-google"></i>
                    </button>
                    <button class="btn btn-outline-primary social-btn">
                        <i class="fab fa-telegram"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="translations.js"></script>
    <script>
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
            // Переключение форм
            const toggleButtons = document.querySelectorAll('.auth-toggle button');
            const forms = document.querySelectorAll('.auth-form');

            toggleButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const formType = button.getAttribute('data-form');
                    
                    // Переключаем активную кнопку
                    toggleButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    // Переключаем активную форму
                    forms.forEach(form => {
                        form.classList.remove('active');
                        if (form.id === `${formType}Form`) {
                            form.classList.add('active');
                        }
                    });
                });
            });

            // Добавляем обработку показа/скрытия пароля
            const passwordToggles = document.querySelectorAll('.password-toggle');
            
            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault(); // Предотвращаем отправку формы
                    
                    const button = e.currentTarget;
                    const input = button.previousElementSibling;
                    const icon = button.querySelector('i');
                    
                    // Сохраняем текущее значение пароля
                    const currentValue = input.value;
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                        button.classList.add('active');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                        button.classList.remove('active');
                    }
                    
                    // Восстанавливаем значение пароля
                    input.value = currentValue;
                });
            });

            // Переключатель темы
            const themeToggle = document.querySelector('.theme-toggle');
            if (themeToggle) {
                // Устанавливаем начальную тему
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.body.classList.toggle('dark-theme', savedTheme === 'dark');

                themeToggle.addEventListener('click', () => {
                    document.body.classList.toggle('dark-theme');
                    const isDark = document.body.classList.contains('dark-theme');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                });
            }

            // Инициалиация языка
            const langButtons = document.querySelectorAll('.lang-toggle button');
            langButtons.forEach(button => {
                button.addEventListener('click', () => {
                    langButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    const lang = button.textContent.toLowerCase();
                    updateLanguage(lang);
                });
            });

            function updateLanguage(lang) {
                const t = translations[lang];
                if (!t) return;

                // Обновляем тексты форм
                document.querySelector('[data-form="login"]').textContent = t.auth.login;
                document.querySelector('[data-form="register"]').textContent = t.auth.register;
                
                // Обновляем плейсхолдеры и тексты входа
                document.querySelectorAll('#loginForm input[type="email"]').forEach(input => {
                    input.placeholder = t.auth.email;
                });
                document.querySelectorAll('#loginForm input[type="password"]').forEach(input => {
                    input.placeholder = t.auth.password;
                });
                document.querySelector('#rememberMe').nextElementSibling.textContent = t.auth.rememberMe;
                document.querySelector('.forgot-password').textContent = t.auth.forgotPassword;
                document.querySelector('#loginForm button[type="submit"]').textContent = t.auth.loginButton;

                // Обновляем форму регистрации
                document.querySelector('#registerForm input[type="text"]').placeholder = t.auth.name;
                document.querySelector('#registerForm input[type="tel"]').placeholder = t.auth.phone;
                document.querySelectorAll('#registerForm input[type="email"]').forEach(input => {
                    input.placeholder = t.auth.email;
                });
                document.querySelectorAll('#registerForm input[type="password"]')[0].placeholder = t.auth.password;
                document.querySelectorAll('#registerForm input[type="password"]')[1].placeholder = t.auth.confirmPassword;
                document.querySelector('#registerForm button[type="submit"]').textContent = t.auth.registerButton;

                // Обновляем селектор атегорий
                const categorySelect = document.querySelector('.category-select');
                if (categorySelect) {
                    // Обновляем placeholder опцию
                    categorySelect.options[0].textContent = t.auth.selectCategory;
                    // Обновляем остальные опции
                    Array.from(categorySelect.options).forEach(option => {
                        const translateKey = option.getAttribute('data-translate');
                        if (translateKey) {
                            const keys = translateKey.split('.');
                            let translation = t;
                            keys.forEach(key => {
                                translation = translation[key];
                            });
                            if (translation) {
                                option.textContent = translation;
                            }
                        }
                    });
                }

                document.querySelector('.social-auth p').textContent = t.auth.orLoginWith;

                localStorage.setItem('selectedLanguage', lang);
            }

            // Загружаем сохраненный язык
            const savedLanguage = localStorage.getItem('selectedLanguage') || 'ru';
            const savedLangButton = Array.from(langButtons).find(
                button => button.textContent.toLowerCase() === savedLanguage
            );
            if (savedLangButton) {
                savedLangButton.click();
            }

            // Обработчик регистрации
            const registerForm = document.getElementById('registerForm');
            if (registerForm) {
                registerForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    showLoading();
                    
                    const responseBlock = document.getElementById('registerApiResponse');
                    const alertBlock = responseBlock.querySelector('.alert');
                    
                    try {
                        // Получаем значения полей, учитывая как password, так и text type
                        const formData = {
                            name: registerForm.querySelector('input[type="text"]').value.trim(),
                            phone: registerForm.querySelector('input[type="tel"]').value.trim(),
                            email: registerForm.querySelector('input[type="email"]').value.trim(),
                            // Изменяем способ получения паролей
                            password: registerForm.querySelector('.input-with-icon input[placeholder="Пароль"]').value,
                            confirmPassword: registerForm.querySelector('.input-with-icon input[placeholder="Подтвердите пароль"]').value,
                            category: registerForm.querySelector('.category-select').value
                        };

                        // Проверка заполнения всех полей
                        for (const [key, value] of Object.entries(formData)) {
                            if (!value) {
                                throw new Error(`Поле ${key} обязательно для заполнения`);
                            }
                        }

                        // Проверка совпадения паролей
                        if (formData.password !== formData.confirmPassword) {
                            throw new Error('Пароли не совпадают');
                        }

                        const response = await fetch('https://bgweb.nurali.uz/api/auth/register', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                name: formData.name,
                                email: formData.email,
                                password: formData.password,
                                phone: formData.phone,
                                category: formData.category
                            })
                        });

                        const data = await response.json();
                        responseBlock.style.display = 'block';

                        if (response.status === 200) {
                            alertBlock.className = 'alert alert-success';
                            alertBlock.textContent = data.message;
                            
                            localStorage.setItem('userRegistrationData', JSON.stringify({
                                email: formData.email,
                                name: formData.name
                            }));
                            
                            setTimeout(() => {
                                window.location.href = 'otp.php';
                            }, 1000);
                        } else {
                            alertBlock.className = 'alert alert-danger';
                            alertBlock.textContent = data.error;
                        }
                    } catch (error) {
                        responseBlock.style.display = 'block';
                        alertBlock.className = 'alert alert-danger';
                        alertBlock.textContent = error.message;
                    } finally {
                        hideLoading();
                    }
                });
            }

            // Обработчик входа
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    showLoading();
                    
                    const responseBlock = document.getElementById('loginApiResponse');
                    const alertBlock = responseBlock.querySelector('.alert');
                    
                    try {
                        const formData = {
                            email: loginForm.querySelector('input[type="email"]').value.trim(),
                            // Изменяем способ получения пароля, используя placeholder для поиска нужного input
                            password: loginForm.querySelector('input[placeholder="Пароль"]').value
                        };

                        const response = await fetch('https://bgweb.nurali.uz/api/auth/login', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(formData)
                        });

                        const data = await response.json();
                        responseBlock.style.display = 'block';

                        if (response.status === 200) {
                            alertBlock.className = 'alert alert-success';
                            alertBlock.textContent = data.message;
                            

                            localStorage.setItem('userEmail',data.user.email);
                            localStorage.setItem('category',data.user.category );
                            localStorage.setItem('verified',data.user.is_verified);
                            localStorage.setItem('userId',data.user.id);
                            
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 1000);
                        } else if (response.status === 401 && data.isVerified === false) {
                            alertBlock.className = 'alert alert-warning';
                            alertBlock.textContent = data.error;
                            
                            localStorage.setItem('userRegistrationData', JSON.stringify({
                                email: formData.email
                            }));
                            
                            setTimeout(() => {
                                window.location.href = 'otp.php';
                            }, 1000);
                        } else {
                            alertBlock.className = 'alert alert-danger';
                            alertBlock.textContent = data.error;
                        }
                    } catch (error) {
                        responseBlock.style.display = 'block';
                        alertBlock.className = 'alert alert-danger';
                        alertBlock.textContent = 'Ошибка при попытке входа';
                    } finally {
                        hideLoading();
                    }
                });
            }

            // Добавьте этот код после инициализации формы
            const passwordInput = registerForm.querySelectorAll('input[type="password"]')[0];
            const confirmPasswordInput = registerForm.querySelectorAll('input[type="password"]')[1];

            // Функция проверки совпадения паролей
            function validatePasswords() {
                if (confirmPasswordInput.value === '') {
                    confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
                    return;
                }
                
                if (passwordInput.value === confirmPasswordInput.value) {
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                } else {
                    confirmPasswordInput.classList.remove('is-valid');
                    confirmPasswordInput.classList.add('is-invalid');
                }
            }

            // Добавляем слушатели событий
            passwordInput.addEventListener('input', validatePasswords);
            confirmPasswordInput.addEventListener('input', validatePasswords);
        });
    </script>
</body>
</html> 