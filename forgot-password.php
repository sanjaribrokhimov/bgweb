<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'components/miniHeader.php'; ?>

    <div class="container-fluid p-3 app-container">
        <div class="text-center mb-4">
            <img src="img/bga.png" alt="BGA" class="header-logo">
        </div>

        <div class="auth-container">
            <h4 class="text-center mb-4" data-translate="auth.forgotPasswordTitle">Восстановление пароля</h4>
            <form id="forgotPasswordForm">
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="form-control" placeholder="Введите ваш email" required data-translate="auth.emailPlaceholder">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100" data-translate="auth.sendCode">Отправить код</button>
                
                <div id="forgotPasswordResponse" class="mt-3" style="display: none;">
                    <div class="alert" role="alert"></div>
                </div>
            </form>
        </div>
    </div>

    <script src="translations.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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

            // Фу��кция обновления переводов
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

            document.getElementById('forgotPasswordForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const email = e.target.querySelector('input[type="email"]').value.trim();
                const responseBlock = document.getElementById('forgotPasswordResponse');
                const alertBlock = responseBlock.querySelector('.alert');
                const submitButton = e.target.querySelector('button[type="submit"]');
                
                if (!email) {
                    responseBlock.style.display = 'block';
                    alertBlock.className = 'alert alert-danger';
                    alertBlock.textContent = translations[savedLang].auth.emailRequired;
                    return;
                }

                try {
                    // Меняем текст кнопки и делаем её неактивной
                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Отправка...';
                    
                    const response = await fetch('https://bgweb.nurali.uz/api/auth/forgot-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ "email": email })
                    });

                    const data = await response.json();
                    responseBlock.style.display = 'block';

                    if (response.status === 200) {
                        alertBlock.className = 'alert alert-success';
                        alertBlock.textContent = data.message;
                        
                        // Сохраняем данные пользователя
                        const userResponse = await fetch(`https://bgweb.nurali.uz/api/auth/user?email=${encodeURIComponent(email)}`);
                        if (userResponse.ok) {
                            const userData = await userResponse.json();
                            const userRegistrationData = {
                                id: userData.id,
                                name: userData.name,
                                email: userData.email,
                                phone: userData.phone,
                                category: userData.category,
                                direction: userData.direction,
                                telegram: userData.telegram,
                                is_verified: userData.is_verified
                            };
                            localStorage.setItem('userRegistrationData', JSON.stringify(userRegistrationData));
                        }
                        
                        localStorage.setItem('resetPasswordEmail', email);
                        localStorage.setItem('resetPassword', 'true');
                        
                        setTimeout(() => {
                            window.location.href = 'otp.php?action=reset-password';
                        }, 1000);
                    } else {
                        alertBlock.className = 'alert alert-danger';
                        alertBlock.textContent = data.error;
                    }
                } catch (error) {
                    console.error('Ошибка:', error);
                    responseBlock.style.display = 'block';
                    alertBlock.className = 'alert alert-danger';
                    alertBlock.textContent = translations[savedLang].auth.serverError;
                } finally {
                    // Возвращаем кнопку в исходное состояние
                    submitButton.disabled = false;
                    submitButton.innerHTML = translations[savedLang].auth.sendCode;
                }
            });
        });
    </script>
</body>
</html> 