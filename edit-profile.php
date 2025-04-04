<!DOCTYPE html>
<html lang="ru">
<script>
    const savedTheme = localStorage.getItem('theme') || 'dark';
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark-theme');
    }
</script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BGA - Редактирование профиля</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'components/miniHeader.php'; ?>

    <div class="container-fluid p-3 app-container">
        <div class="text-center mb-4">
            <img src="img/logo.png" alt="BGA" class="header-logo" style="height: 100px">
        </div>

        <div class="auth-container">
            <h4 class="text-center mb-4 translate">Заполнение профиля</h4>
            <form id="editProfileForm">
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" name="name" placeholder="Имя" required>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="tel" class="form-control" name="phone" placeholder="Телефон" required autocomplete="off">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fab fa-telegram"></i>
                        <input type="text" class="form-control" name="telegram" placeholder="Telegram" required>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fab fa-instagram"></i>
                        <input type="text" class="form-control" name="instagram" placeholder="Instagram" required>
                    </div>
                </div>

                <div hidden class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-users"></i>
                        <select class="form-select" name="category" required>
                            <option class="translate" value="">Выберите категорию</option>
                            <option class="translate" value="blogger">Блогер</option>
                            <option class="translate" value="company">Компания</option>
                            <option class="translate" value="freelancer">Фрилансер</option>
                        </select>
                    </div>
                </div>

                <div class="direction-selects-container">
                    <div class="input-with-icon">
                        <i class="fas fa-stream"></i>
                        <select class="form-select direction-blogger" style="display: none;">
                            <option class="translate" value="" disabled selected>Выберите направление</option>
                            <option class="translate" value="lifestyle">Лайфстайл и влог</option>
                            <option class="translate" value="fashion">Мода и стиль</option>
                            <option class="translate" value="beauty">Бьюти и косметика</option>
                            <option class="translate" value="travel">Путешествия и туризм</option>
                            <option class="translate" value="food">Еда и кулинария</option>
                            <option class="translate" value="sport">Спорт и фитнес</option>
                            <option class="translate" value="business">Бизнес и предпринимательство</option>
                            <option class="translate" value="education">Образование и саморазвитие</option>
                            <option class="translate" value="technology">Технологии и гаджеты</option>
                            <option class="translate" value="gaming">Игры и киберспорт</option>
                            <option class="translate" value="music">Музыка и развлечения</option>
                            <option class="translate" value="art">Искусство и творчество</option>
                            <option class="translate" value="health">Здоровье и wellness</option>
                            <option class="translate" value="parenthood">Родительство и семья</option>
                            <option class="translate" value="pets">Домашние животные</option>
                            <option class="translate" value="cars">Автомобили и транспорт</option>
                            <option class="translate" value="finance">Финансы и инвестиции</option>
                            <option class="translate" value="motivation">Мотивация и психология</option>
                        </select>

                        <select class="form-select direction-company" style="display: none;">
                            <option class="translate" value="" disabled selected>Выберите направление</option>
                            <option class="translate" value="retail">Розничная торговля</option>
                            <option class="translate" value="wholesale">Оптовая торговля</option>
                            <option class="translate" value="services">Услуги и сервис</option>
                            <option class="translate" value="manufacturing">Производство</option>
                            <option class="translate" value="tech">IT и технологии</option>
                            <option class="translate" value="finance">Финансы и банкинг</option>
                            <option class="translate" value="construction">Строительство</option>
                            <option class="translate" value="realestate">Недвижимость</option>
                            <option class="translate" value="healthcare">Здравоохранение</option>
                            <option class="translate" value="education">Образование</option>
                            <option class="translate" value="hospitality">Гостиничный бизнес</option>
                            <option class="translate" value="restaurant">Рестораны и общепит</option>
                            <option class="translate" value="logistics">Логистика</option>
                            <option class="translate" value="agriculture">Сельское хозяйство</option>
                            <option class="translate" value="energy">Энергетика</option>
                            <option class="translate" value="media">Медиа и развлечения</option>
                            <option class="translate" value="consulting">Консалтинг</option>
                            <option class="translate" value="automotive">Автомобильный бизнес</option>
                            <option class="translate" value="mall">Торговые центры</option>
                        </select>

                        <select class="form-select direction-freelancer" style="display: none;">
                            <option class="translate" value="" disabled selected>Выберите направление</option>
                            <option class="translate" value="webdev">Веб-разработка</option>
                            <option class="translate" value="mobiledev">Мобильная разработка</option>
                            <option class="translate" value="uidesign">UI/UX Дизайн</option>
                            <option class="translate" value="graphicdesign">Графический дизайн</option>
                            <option class="translate" value="marketing">Маркетинг</option>
                            <option class="translate" value="smm">SMM</option>
                            <option class="translate" value="copywriting">Копирайтинг</option>
                            <option class="translate" value="translation">Перевод</option>
                            <option class="translate" value="video">Видеопроизводство</option>
                            <option class="translate" value="animation">Анимация</option>
                            <option class="translate" value="voiceover">Озвучка</option>
                            <option class="translate" value="photography">Фотография</option>
                            <option class="translate" value="3d">3D моделирование</option>
                            <option class="translate" value="gamedev">Разработка игр</option>
                            <option class="translate" value="seo">SEO-оптимизация</option>
                            <option class="translate" value="analytics">Аналитика</option>
                            <option class="translate" value="consulting">Консультирование</option>
                            <option class="translate" value="projectmanagement">Управление проектами</option>
                        </select>
                    </div>
                </div>
                <div class="change-password-div" title="Можно не заполнять чтобы оставить старый пароль!">
                    <span class="ch-p-label translate">Новый пароль</span>
                    <div class="form-group mb-3">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control translate" name="password" placeholder="Новый пароль" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control translate" name="confirmPassword" placeholder="Подтвердите пароль" autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <style>
                    .change-password-div
                    {
                        border: 1px solid #2c3034;
                        padding: 25px 15px 0;
                        position: relative;
                        margin-bottom: 15px;
                        border-radius: 8px;
                    }
                    .ch-p-label
                    {
                        position: absolute;
                        top: 0;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        z-index: 9999;
                        background: var(--primary-bg);
                        padding: 0 10px;
                    }
                    /* Общие стили для всех полей ввода и селекторов */
                    .form-control, .form-select {
                       
                        border: 1px solid var(--border-color);
                        border-radius: 8px;
                        color: var(--text-color);
                        height: calc(3rem + 2px);
                        padding-left: 40px;
                        font-size: 1rem;
                    }

                    /* Стили для темной темы */
                    .dark-theme .form-control,
                    .dark-theme .form-select {
                      
                        border: 1px solid #2c3034;
                        color: var(--dark-text-color);
                    }

                    /* Стили при фокусе */
                    .form-control:focus,
                    .form-select:focus {
                        background: var(--dark-input-bg);
                        border-color: var(--accent-blue);
                        box-shadow: none;
                        color: var(--text-color);
                    }

                    .direction-selects-container {
                        margin-bottom: 1rem;
                        display: none;
                    }
                    .direction-selects-container select {
                        margin-top: 0.5rem;
                    }
                    .direction-selects-container select option {
                     
                        color: var(--text-color);
                        padding: 8px;
                        cursor: pointer;
                    }
                    .dark-theme .direction-selects-container select option {
                  
                        color: var(--dark-text-color);
                    }
                    .input-with-icon {
                        position: relative;
                        width: 100%;
                    }
                    .input-with-icon i {
                        position: absolute;
                        left: 14px;
                        top: 50%;
                        transform: translateY(-50%);
                        color: var(--text-color);
                        opacity: 0.7;
                        z-index: 1;
                        pointer-events: none;
                    }
                    .auth-container {
                        max-width: 450px;
                        margin: 0 auto;
                        padding: 2rem;
                        background: var(--dark-bg);
                        border-radius: 1rem;
                        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    }
                    .btn-primary {
                        height: calc(3rem + 2px);
                        font-size: 1rem;
                        background-color: var(--accent-blue);
                        border: none;
                        border-radius: 0.5rem;
                    }
                    .btn-primary:hover {
                        background-color: var(--accent-blue-hover);
                    }
                    .form-group {
                        margin-bottom: 1.5rem;
                    }
                </style>

                <button type="submit" class="btn btn-primary w-100 translate">Сохранить</button>
                
                <div id="editProfileResponse" class="mt-3" style="display: none;">
                    <div class="alert" role="alert"></div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const form = document.getElementById('editProfileForm');
            
            // Получаем данные пользователя из базы
            try {
                // const email = localStorage.getItem('userEmail');
                // const tg_chat_id = localStorage.getItem('telegram_chat_id') || null;
                // const identifier = tg_chat_id ? tg_chat_id : email;
                const userId = localStorage.getItem('userId');
                const response = await fetch(`https://blogy.uz/api/auth/check-fields/${userId}`);
                
                if (!response.ok) {
                    throw new Error('Ошибка получения данных пользователя');
                }

                const userData = await response.json();
                console.log('Полученные данные:', userData); // Для отладки
                
                const is_complete = userData.is_complete;
                if(!is_complete){
                    window.location.href = 'reRegister.php';
                    // console.error('Ошибка:', error);
                    // Можно показать сообщение об ошибке пользователю
                    // const responseBlock = document.getElementById('editProfileResponse');
                    // const alertBlock = responseBlock.querySelector('.alert');
                    // responseBlock.style.display = 'block';
                    // alertBlock.className = 'alert alert-danger';
                    // alertBlock.textContent = 'Заполните профиль чтобы продолжить работу в приложении';
                }
                const user = userData.user;
                for(let key in user){
                    if(user[key] === 'empty'){
                        user[key] = '';
                    }
                }
                
                // Заполняем форму данными из базы
                form.name.value = user.name || '';
                form.phone.value = user.phone || '';
                form.telegram.value = user.telegram || ''; // Исправлено имя поля
                form.instagram.value = user.instagram || '';
                form.category.value = user.category || '';
                
                // Показываем соответствующий select направления
                if (user.category) {
                    const directionContainer = document.querySelector('.direction-selects-container');
                    const directionSelect = document.querySelector(`.direction-${user.category}`);
                    if (directionSelect) {
                        directionContainer.style.display = 'block';
                        directionSelect.style.display = 'block';
                        directionSelect.value = user.direction || '';
                        // Если направление не выбралось, выводим в консоль для отладки
                        if (!directionSelect.value) {
                            console.log('Направление не найдено:', user.direction);
                            console.log('Доступные опции:', Array.from(directionSelect.options).map(opt => opt.value));
                        }
                    }
                }
            } catch (error) {
                console.error('Ошибка:', error);
                // Можно показать сообщение об ошибке пользователю
                const responseBlock = document.getElementById('editProfileResponse');
                const alertBlock = responseBlock.querySelector('.alert');
                responseBlock.style.display = 'block';
                alertBlock.className = 'alert alert-danger';
                alertBlock.textContent = 'Ошибка получения данных профиля';
            }

            // Обработчик изменения категории
            form.category.addEventListener('change', function() {
                const selectedCategory = this.value;
                const directionContainer = document.querySelector('.direction-selects-container');
                const directionSelects = {
                    blogger: document.querySelector('.direction-blogger'),
                    company: document.querySelector('.direction-company'),
                    freelancer: document.querySelector('.direction-freelancer')
                };
                
                // Сначала скрываем контейнер
                directionContainer.style.display = 'none';
                
                Object.values(directionSelects).forEach(select => {
                    select.style.display = 'none';
                    select.removeAttribute('name');
                    select.removeAttribute('required');
                });
                
                if (!selectedCategory) {
                    return;
                }
                
                // Показываем контейнер только если выбрана категория
                directionContainer.style.display = 'block';
                
                const directionSelect = document.querySelector(`.direction-${selectedCategory}`);
                if (directionSelect) {
                    directionSelect.style.display = 'block';
                    directionSelect.setAttribute('name', 'direction');
                    directionSelect.setAttribute('required', 'required');
                    directionSelect.selectedIndex = 0;
                }
            });

            // Скрываем контейнер направлений при загрузке, если категория не выбрана
            if (!form.category.value) {
                document.querySelector('.direction-selects-container').style.display = 'none';
            }

            // Обрботчик отправки формы
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitButton = form.querySelector('button[type="submit"]');
                const responseBlock = document.getElementById('editProfileResponse');
                const alertBlock = responseBlock.querySelector('.alert');

                try {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Сохранение...';

                    if (form.password.value !== form.confirmPassword.value) {
                        throw new Error('Пароли не совпадают');
                    }

                    const formData = {
                        email: localStorage.getItem('userEmail')
                    };

                    // Добавляем только заполненные поля
                    formData.tg_chat_id = "chat_id";
                    formData.tg_user_id = "user_id";
                    if (form.name.value) formData.name = form.name.value;
                    if (form.phone.value) formData.phone = form.phone.value;
                    if (form.telegram.value) formData.telegram = form.telegram.value;
                    if (form.instagram.value) formData.instagram = form.instagram.value;
                    if (form.category.value) {
                        formData.category = form.category.value;
                        const directionSelect = document.querySelector(`.direction-${form.category.value}`);
                        if (directionSelect && directionSelect.value) {
                            formData.direction = directionSelect.value;
                        }
                    }
                    if (form.password.value) formData.password = form.password.value;

                    const response = await fetch('https://blogy.uz/api/auth/update-profile', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    console.log('Request URL:', 'https://blogy.uz/api/auth/update-profile');
                    console.log('Request method:', 'POST');
                    console.log('Request body:', formData);
                    console.log('Response status:', response.status);

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Response error:', errorText);
                        throw new Error(`Ошибка сервера: ${response.status}`);
                    }

                    const data = await response.json();

                    if (response.ok) {
                        localStorage.setItem('userRegistrationData', JSON.stringify(formData));

                        responseBlock.style.display = 'block';
                        alertBlock.className = 'alert alert-success';
                        alertBlock.textContent = 'Профиль успешно обновлен';

                        setTimeout(() => {
                            localStorage.clear();
                            window.location.href = 'login.php';
                        }, 1000);
                    } else {
                        throw new Error(data.error || 'Ошибка при обновлении профиля');
                    }
                } catch (error) {
                    responseBlock.style.display = 'block';
                    alertBlock.className = 'alert alert-danger';
                    alertBlock.textContent = error.message;
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Сохранить';
                }
            });
            
            translatePage(localStorage.selectedLanguage || "ru");
        });
    </script>
</body>
</html> 