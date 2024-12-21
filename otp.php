<?php include 'components/miniHeader.php'; ?>

<!-- Индикатор загрузки -->
<div id="loadingIndicator" class="loading-indicator" style="display: none;">
    <div class="spinner-wrapper">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="loading-text">Пожалуйста, подождите...</div>
    </div>
</div>

<div class="auth-container">
    <form id="otpForm" class="auth-form active">
        <h2 class="text-center mb-4" data-translate="auth.otpTitle">Подтверждение</h2>
        <p class="text-center mb-4" id="otpDescription">
            Введите код подтверждения, отправленный на email: <span id="userEmail"></span>
        </p>

        <div class="otp-inputs mb-4">
            <input type="text" maxlength="1" class="otp-input" required>
            <input type="text" maxlength="1" class="otp-input" required>
            <input type="text" maxlength="1" class="otp-input" required>
            <input type="text" maxlength="1" class="otp-input" required>
        </div>

        <div class="resend-container text-center mb-4">
            <div id="timerBlock">
                <span data-translate="auth.otpTimer">Повторная отправка через:</span> 
                <span id="timer">60</span>с
            </div>
            <button type="button" id="resendBtn" class="btn-link" style="display: none;" data-translate="auth.otpResend">
                Отправить код повторно
            </button>
        </div>

        <button type="submit" class="btn btn-primary w-100" data-translate="auth.otpVerify">
            Подтвердить
        </button>

        <!-- Добавляем блок для отображения ответа -->
        <div id="apiResponse" class="mt-3" style="display: none;">
            <div class="alert" role="alert"></div>
        </div>
    </form>
</div>

<script src="translations.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');
    const isResetPassword = localStorage.getItem('resetPassword') === 'true';
    const email = isResetPassword ? localStorage.getItem('resetPasswordEmail') : localStorage.getItem('userEmail');
    
    // Обновляем текст описания в зависимости от действия
    const otpDescription = document.getElementById('otpDescription');
    if (isResetPassword) {
        otpDescription.textContent = `Введите код подтверждения, отправленный на email: ${email}`;
    }

    // Переключатели языка
    const langButtons = document.querySelectorAll('.lang-toggle button');
    const savedLang = localStorage.getItem('selectedLanguage') || 'ru';

    // Устанавливаем активную кнопку языка
    langButtons.forEach(button => {
        if (button.textContent.toLowerCase() === savedLang) {
            button.classList.add('active');
        }
    });

    // Обработчик переключения языка
    langButtons.forEach(button => {
        button.addEventListener('click', () => {
            const lang = button.textContent.toLowerCase();
            langButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            localStorage.setItem('selectedLanguage', lang);
            updateTranslations(lang);
        });
    });

    // Переключатель темы
    const themeToggle = document.querySelector('.theme-toggle');
    const savedTheme = localStorage.getItem('theme') || 'dark';

    // Устанавливаем сохраненную тему
    if (savedTheme === 'light') {
        document.body.classList.add('light-theme');
    }

    // Обработчик переключения темы
    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('light-theme');
        localStorage.setItem('theme', document.body.classList.contains('light-theme') ? 'light' : 'dark');
    });

    // Функция обновления переводов
    function updateTranslations(lang) {
        document.querySelectorAll('[data-translate]').forEach(element => {
            const key = element.getAttribute('data-translate');
            try {
                const keys = key.split('.');
                let translation = translations[lang];
                keys.forEach(k => translation = translation[k]);
                element.tagName === 'INPUT' ? element.placeholder = translation : element.textContent = translation;
            } catch (e) {
                console.error('Translation error:', e);
            }
        });
    }

    // Инициализация переводов
    updateTranslations(savedLang);

    // OTP инпуты
    const otpInputs = document.querySelectorAll('.otp-input');
    
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
    });

    // Обратный отсчет
    const timerElement = document.getElementById('timer');
    const resendBtn = document.getElementById('resendBtn');
    const timerBlock = document.getElementById('timerBlock');
    let timeLeft = 60;

    function startTimer() {
        timeLeft = 60;
        timerElement.textContent = timeLeft;
        timerBlock.style.display = 'block';
        resendBtn.style.display = 'none';

        const interval = setInterval(() => {
            timeLeft--;
            timerElement.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(interval);
                timerBlock.style.display = 'none';
                resendBtn.style.display = 'block';
            }
        }, 1000);

        return interval;
    }

    // Запускаем таймер при загрузке страницы
    let currentInterval = startTimer();

    // Обновляем функцию обработки повторной отправки кода
    resendBtn.addEventListener('click', async () => {
        const loadingIndicator = document.getElementById('loadingIndicator');
        const responseBlock = document.getElementById('apiResponse');
        const alertBlock = responseBlock.querySelector('.alert');
        
        try {
            showLoading();
            
            // Получаем email из localStorage
            const userData = JSON.parse(localStorage.getItem('userRegistrationData'));
            
            // Отправляем запрос на повторную отправку OTP
            const response = await fetch('http://localhost:8888/api/auth/resend-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: userData.email
                })
            });

            const data = await response.json();
            responseBlock.style.display = 'block';
            
            if (response.status === 200) {
                alertBlock.className = 'alert alert-success';
                alertBlock.textContent = 'Новый код отправлен';
                
                // Перезапускаем таймер
                if (currentInterval) {
                    clearInterval(currentInterval);
                }
                currentInterval = startTimer();
            } else {
                alertBlock.className = 'alert alert-danger';
                alertBlock.textContent = data.error || 'Ошибка при отправке кода';
            }
        } catch (error) {
            console.error('Error:', error);
            responseBlock.style.display = 'block';
            alertBlock.className = 'alert alert-danger';
            alertBlock.textContent = 'Ошибка при отправке запроса';
        } finally {
            hideLoading();
        }
    });

    // Обработка формы
    const otpForm = document.getElementById('otpForm');
    otpForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const loadingIndicator = document.getElementById('loadingIndicator');
        const responseBlock = document.getElementById('apiResponse');
        const alertBlock = responseBlock.querySelector('.alert');
        const otpInputs = document.querySelectorAll('.otp-input');
        
        try {
            showLoading();
            
            // Получаем введенный OTP код
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            
            // Проверяем, что введены все цифры
            if (otp.length !== 4) {
                throw new Error('Введите се 4 цифры кода');
            }
            
            // Получаем данные пользователя из localStorage
            const userData = JSON.parse(localStorage.getItem('userRegistrationData'));
            
            const response = await fetch('http://localhost:8888/api/auth/verify-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: userData.email,
                    otp: otp
                })
            });

            const data = await response.json();
            responseBlock.style.display = 'block';
            
            if (response.status === 200) {
                // Успешная верификация
                alertBlock.className = 'alert alert-success';
                alertBlock.textContent = data.message;

                // Получаем данные о пользователе
                const userResponse = await fetch(`http://localhost:8888/api/auth/user?email=${userData.email}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (userResponse.ok) {
                    const userData = await userResponse.json();
                    // Сохраняем данные пользователя в localStorage
                    localStorage.setItem('userEmail',userData.email);
                    localStorage.setItem('category',userData.category );
                    localStorage.setItem('verified',userData.is_verified);
                    localStorage.setItem('userId',userData.id);
                    localStorage.setItem('direction',userData.direction);
                    localStorage.setItem('telegram',userData.telegram);
                    localStorage.setItem('name',userData.name);
                    localStorage.setItem('phone',userData.phone);
                }
                
                // Очищаем поля ввода
                otpInputs.forEach(input => {
                    input.value = '';
                    input.classList.remove('is-invalid');
                });
                
                // Удаляем временные данные регистрации
                localStorage.removeItem('userRegistrationData');
                
                const isResetPassword = localStorage.getItem('resetPassword') === 'true';
                
                if (isResetPassword) {
                    // Если это сброс пароля, перенаправляем на edit-profile.php
                    localStorage.removeItem('resetPasswordEmail');
                    localStorage.removeItem('resetPassword');
                    window.location.href = 'edit-profile.php';
                } else {
                    // Если это обычная регистрация, перенаправляем на index.php
                    window.location.href = 'index.php';
                }
            } else {
                // Неверный код
                alertBlock.className = 'alert alert-danger';
                alertBlock.textContent = 'Неверный код подтверждения';
                
                // Подсвечиваем поля красным
                otpInputs.forEach(input => {
                    input.classList.add('is-invalid');
                });
                
                // Очищаем поля для нового ввода
                otpInputs.forEach(input => {
                    input.value = '';
                });
                
                // Фокус на первое поле
                otpInputs[0].focus();
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

    // Добавляем отображение email
    const userData = JSON.parse(localStorage.getItem('userRegistrationData'));
    if (userData && userData.email) {
        document.getElementById('userEmail').textContent = userData.email;
    }
});

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
</script>

<style>
.otp-inputs {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.otp-input {
    width: 50px;
    height: 50px;
    text-align: center;
    font-size: 24px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: var(--card-bg);
    color: var(--text-color);
}

.otp-input:focus {
    border-color: var(--accent-blue);
    outline: none;
    box-shadow: 0 0 0 2px rgba(77, 126, 255, 0.2);
}

.resend-container {
    color: var(--text-secondary);
}

.btn-link {
    background: none;
    border: none;
    color: var(--accent-blue);
    text-decoration: underline;
    padding: 0;
    margin: 0;
    cursor: pointer;
}

.btn-link:disabled {
    color: var(--text-secondary);
    cursor: not-allowed;
    text-decoration: none;
}

.timer {
    display: block;
    margin-bottom: 10px;
}

.otp-input.is-invalid {
    border-color: var(--danger-color, #dc3545);
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.alert {
    padding: 12px;
    border-radius: 8px;
    margin-top: 15px;
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