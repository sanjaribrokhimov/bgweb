<!DOCTYPE html>
<html lang="ru">
<script>
    // Добавляем скрипт инициализации темы до загрузки DOM
    const savedTheme = localStorage.getItem('theme') || 'dark';
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark-theme');
    }
</script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BGA - login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/register.css">
</head>
<body>
  

    <div class="container-fluid p-3 app-container">
       
    <?php include 'components/miniHeader.php'; ?>
    

        <!-- Форма входа/регистрации -->
        <div class="auth-container">
            <h2 class="text-center p-4">Полная регистрация</h2>
            <form id="reRegisterForm" class="auth-form active" autocomplete="off">
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input name="name" type="text" class="form-control" placeholder="Имя" required autocomplete="off">
                    </div>
                </div>

                <!-- Добавляем поле для номера телефона -->
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="tel" class="form-control" placeholder="Номер телефона" required autocomplete="off" 
                                pattern="[\+]?[0-9]{12}" title="Формат: +998 XX XXX XX XX" name="phone">
                    </div>
                </div>

                <!-- Селектор категории -->
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-tag"></i>
                        <select class="form-control category-select" required autocomplete="off" name="category">
                            <option value="" disabled selected data-translate="auth.selectCategory">Выберите категорию</option>
                            <option value="blogger" data-translate="auth.categories.blogger">Блогер</option>
                            <option value="company" data-translate="auth.categories.company">Компания</option>
                            <option value="freelancer" data-translate="auth.categories.freelancer">Фрилансер</option>
                        </select>
                    </div>
                </div>

                <!-- После селектора категории -->
                <div class="form-group mb-3 direction-selects-container" style="display: none;">
                    <div class="input-with-icon">
                        <i class="fas fa-compass"></i>

                        <!-- Направления для блогеров -->
                        <select class="form-control direction-blogger" autocomplete="off" style="display: none;">
                            <option value="" disabled selected>Выберите направление</option>
                            <option value="lifestyle">Лайфстайл и влог</option>
                            <option value="fashion">Мода и стиль</option>
                            <option value="beauty">Бьюти и косметика</option>
                            <option value="travel">Путешествия и туризм</option>
                            <option value="food">Еда и кулинария</option>
                            <option value="sport">Спорт и фитнес</option>
                            <option value="business">Бизнес и предпринимательство</option>
                            <option value="education">Образование и саморазвитие</option>
                            <option value="technology">Технологии и гаджеты</option>
                            <option value="gaming">Игры и киберспорт</option>
                            <option value="music">Музыка и развлечения</option>
                            <option value="art">Искусство и творчество</option>
                            <option value="health">Здоровье и wellness</option>
                            <option value="parenthood">Родительство и семья</option>
                            <option value="pets">Домашние животные</option>
                            <option value="cars">Автомобили и транспорт</option>
                            <option value="finance">Финансы и инвестиции</option>
                            <option value="motivation">Мотивация и психология</option>
                        </select>

                        <!-- Направления для компаний -->
                        <select class="form-control direction-company" autocomplete="off" style="display: none;">
                            <option value="" disabled selected>Выберите направление</option>
                            <option value="retail">Розничная торговля</option>
                            <option value="wholesale">Оптовая торговля</option>
                            <option value="services">Услуги и сервис</option>
                            <option value="manufacturing">Производство</option>
                            <option value="tech">IT и технологии</option>
                            <option value="finance">Финансы и банкинг</option>
                            <option value="construction">Строительство</option>
                            <option value="realestate">Недвижимость</option>
                            <option value="healthcare">Здравоохранение</option>
                            <option value="education">Образование</option>
                            <option value="hospitality">Гостиничный бизнес</option>
                            <option value="restaurant">Рестораны и общепит</option>
                            <option value="logistics">Логистика</option>
                            <option value="agriculture">Сельское хозяйство</option>
                            <option value="energy">Энергетика</option>
                            <option value="media">Медиа и развлечения</option>
                            <option value="consulting">Консалтинг</option>
                            <option value="automotive">Автомобильный бизнес</option>
                            <option value="mall">Торговые центры</option>
                        </select>

                        <!-- Направления для фрилансеров -->
                        <select class="form-control direction-freelancer" autocomplete="off" style="display: none;">
                            <option value="" disabled selected>Выберите направление</option>
                            <option value="webdev">Веб-разработка</option>
                            <option value="mobiledev">Мобильная разработка</option>
                            <option value="uidesign">UI/UX Дизайн</option>
                            <option value="graphicdesign">Графический дизайн</option>
                            <option value="marketing">Маркетинг</option>
                            <option value="smm">SMM</option>
                            <option value="copywriting">Копирайтинг</option>
                            <option value="translation">Перевод</option>
                            <option value="video">Видеопроизводство</option>
                            <option value="animation">Анимация</option>
                            <option value="voiceover">Озвучка</option>
                            <option value="photography">Фотография</option>
                            <option value="3d">3D моделирование</option>
                            <option value="gamedev">Разработка игр</option>
                            <option value="seo">SEO-оптимизация</option>
                            <option value="analytics">Аналитика</option>
                            <option value="consulting">Консультирование</option>
                            <option value="projectmanagement">Управление проектами</option>
                        </select>
                    </div>
                </div>
                
                <!-- Добавляем поле для Telegram username -->
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fab fa-telegram"></i>
                        <input type="text" 
                                class="form-control" 
                                name="telegram"
                                required
                                placeholder="@sanjar_3210"
                                autocomplete="off">
                    </div>
                    <!-- <div class="input-hint">
                        <i class="fas fa-circle-info"></i>
                        Например: @sanjar_3210
                    </div> -->
                </div>
                
                <div class="form-group mb-3 hidden" id="registerEmailBlock">
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="form-control" placeholder="Email" name="email" autocomplete="off">
                    </div>
                </div>          

                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fab fa-instagram"></i>
                        <input type="text" 
                                class="form-control" 
                                name="instagram"
                                placeholder="сылка на Instagram"
                                data-placeholder="instagram"
                                required 
                                autocomplete="off">
                    </div>
                    <div class="input-hint">
                        <i class="fas fa-circle-info"></i>
                        Вставьте ссылку на ваш Instagram
                    </div>
                </div>
                        
                <button type="submit" class="btn btn-primary w-100">Отправить данные</button>
                
                <!-- Изменяем id для блока ответа -->
                <div id="registerApiResponse" class="mt-3" style="display: none;">
                    <div class="alert" role="alert"></div>
                </div>
                
                <!-- Изменяем id для блока ответа -->
                <div id="registerApiResponse" class="mt-3" style="display: none;">
                    <div class="alert" role="alert"></div>
                </div>
            </form>
        </div>
    </div>

    <script>
        
        const responseBlock = document.getElementById('registerApiResponse');
        const alertBlock = responseBlock.querySelector('.alert');
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
            

            // Обработчик изменения категории
            const categorySelect = document.querySelector('.category-select');
            const directionContainer = document.querySelector('.direction-selects-container');
            const directionSelects = {
                blogger: document.querySelector('.direction-blogger'),
                company: document.querySelector('.direction-company'),
                freelancer: document.querySelector('.direction-freelancer')
            };

            categorySelect.addEventListener('change', function() {
                const selectedCategory = this.value;
                
                // Сначала скрываем все селекты и убираем required
                Object.values(directionSelects).forEach(select => {
                    select.style.display = 'none';
                    select.removeAttribute('name');
                    select.removeAttribute('required'); // Убираем required у скрытых селектов
                });
                
                // Снчала проверяем выбрана ли категория
                if (!selectedCategory) {
                    directionContainer.style.display = 'none';
                    return;
                }
                
                // Показываем контейнер если выбрана категория
                directionContainer.style.display = 'block';
                
                // Показываем нужный селект
                const directionSelect = directionSelects[selectedCategory];
                if (directionSelect) {
                    directionSelect.style.display = 'block';
                    directionSelect.setAttribute('name', 'direction');
                    directionSelect.setAttribute('required', 'required'); // Добавляем required только активном селекту
                    console.log(directionSelect);
                    
                    // Сбрасываем выбранное значение
                    directionSelect.selectedIndex = 0;
                }
            });
        })

        // Обработчик регистрации
        const registerForm = document.getElementById('reRegisterForm');
        if (registerForm) {
            registerForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const direction = registerForm.querySelector(`[name="direction"]`)?.value;
                const telegram = registerForm.querySelector('input[name="telegram"]')?.value.trim();

                if (!direction) {
                    const alertBlock = document.getElementById('registerApiResponse').querySelector('.alert');
                    alertBlock.className = 'alert alert-danger';
                    alertBlock.textContent = 'Пожалуйста, выберите направление';
                    alertBlock.style.display = 'block';
                    return;
                }

                if (!telegram) {
                    const alertBlock = document.getElementById('registerApiResponse').querySelector('.alert');
                    alertBlock.className = 'alert alert-danger';
                    alertBlock.textContent = 'Пожалуйста, укажите Telegram username';
                    alertBlock.style.display = 'block';
                    return;
                }

                try {
                    // Изменяем способ получения пароля
                    const formData = {
                        name: registerForm.querySelector('input[name="name"]').value.trim(),
                        phone: registerForm.querySelector('input[name="phone"]').value.trim(),
                        email: registerForm.querySelector('input[name="email"]').value.trim(),
                        category: registerForm.querySelector('.category-select').value,
                        direction: registerForm.querySelector(`[name="direction"]`)?.value || '',
                        telegram: registerForm.querySelector('input[name="telegram"]')?.value.trim(),
                        instagram: registerForm.querySelector('input[name="instagram"]')?.value.trim(),
                    };

                    // Логируем данные
                    console.log('Отправляемые данные (register):', formData);

                    const userId = localStorage.getItem('userId');

                    if(!userId){
                        const alertBlock = document.getElementById('registerApiResponse').querySelector('.alert');
                        alertBlock.className = 'alert alert-danger';
                        alertBlock.textContent = 'Пожалуйста, войдите в систему';
                        alertBlock.style.display = 'block';
                        return;
                    }

                    const response = await fetch(`http://localhost:8888/api/auth/complete-registration/${userId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();
                    // Логируем ответ
                    console.log('Ответ сервера (register):', data);
                    console.log('Статус ответа:', response.status);

                    responseBlock.style.display = 'block';

                    if (response.status === 200) {
                        alertBlock.className = 'alert alert-success';
                        alertBlock.textContent = data.message;
                        
                        if(data.message !== 'Регистрация успешно завершена'){
                            setTimeout(() => {
                                window.location.href = 'otp.php';
                            }, 1000);
                        }else
                        {
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 1000);
                        }
                    } else {
                        alertBlock.className = 'alert alert-danger';
                        alertBlock.textContent = data.error;
                    }
                } catch (error) {
                    console.error('Ошибка при регистрации:', error);
                    responseBlock.style.display = 'block';
                    alertBlock.className = 'alert alert-danger';
                    alertBlock.textContent = error.message;
                } finally {
                    hideLoading();
                }
            });
        }
    </script>
</body>
</html>
