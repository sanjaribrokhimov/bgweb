<?php
$IP = '144.126.128.67';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloger Agency</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1.0.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    

    <style>
        .product-card {
            max-width: 180px;
        }
        /* Фиксируем блок сверху */
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: var(--card-bg);
            transition: all 0.3s ease;
            padding: 12px 15px;
            border-radius: 16px;
            margin: 10px 15px;
            box-shadow: var(--shadow);
            transform: translateY(0);
        }

        .top-bar.hidden {
            transform: translateY(-150%);
        }

        /* Добавляем отступ для контента под fixed top-bar */
        .app-container {
            padding-top: 60px !important;
        }

        /* Стили для header с логотипом и поиском */
        header {
            margin-top: 60px;
            position: relative;
            z-index: 999;
        }

        /* Стили для поиска и уведомлений */
        .notifications-wrapper,
        .search-container {
            position: relative;
            z-index: 999;
        }

        /* Стили для логотипа */
        .header-logo {
            height: 40px;
            position: relative;
            z-index: 999;
        }

        /* Стили для индикатора загрузки */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
            visibility: hidden; /* Скрыть по умолчанию */
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .loading-overlay.active {
            visibility: visible;
            opacity: 1;
        }

        .confirm-modal {
            position: fixed;
            display: none;
            background: rgba(44, 44, 44, 0.95);
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transform: translate(-50%, -50%) scale(0.8);
            transition: all 0.3s ease;
        }

        .confirm-modal.active {
            display: flex;
        }

        .confirm-modal.shake {
            animation: shake 0.5s ease-in-out;
        }

        .confirm-content {
            display: flex;
            gap: 15px;
        }

        .confirm-yes, .confirm-no {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .confirm-yes {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            color: white;
        }

        .confirm-no {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
        }

        .confirm-yes:hover, .confirm-no:hover {
            transform: scale(1.1);
        }

        @keyframes shake {
            0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
            25% { transform: translate(-50%, -50%) rotate(-5deg); }
            75% { transform: translate(-50%, -50%) rotate(5deg); }
        }

        .notification-badge {
            font-size: 0.6rem;
            transform: translate(-50%, -50%) !important;
        }
        
        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        
        
        .notification-time {
            font-size: 0.8rem;
            color: #666;
        }
        
        .notification-content {
            margin-top: 5px;
        }

        /* Стили для контейнера уведомлений */
        .notifications-wrapper {
            position: relative;
        }

        /* Стили для кнопки уведомлени */
        .notification-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-1);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .notification-btn:hover {
            transform: translateY(-2px);
            background: var(--card-hover);
        }

        .notification-btn i {
            color: var(--text-color);
            font-size: 16px;
        }

        /* Стили для бейджа уведомлений */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent-blue);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: none;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--card-bg);
        }

        /* Обновленные стили для поиска */
        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-container.search-active {
            background: var(--card-hover);
        }

        .search-input {
            position: absolute;
            right: 45px;
            width: 0;
            height: 40px;
            padding: 0;
            border: none;
            border-radius: 20px;
            background: var(--gradient-1);
            color: var(--text-color);
            transition: all 0.3s ease;
            opacity: 0;
            pointer-events: none;
        }

        .search-input.active {
            width: 200px;
            padding: 0 15px;
            opacity: 1;
            pointer-events: auto;
        }





        @media (max-width: 768px) {
            .search-input.active {
                width: 150px;
            }
        }

        /* Стили для скрытия контента */
        .header-content {
            transition: all 0.3s ease;
            opacity: 1;
            visibility: visible;
        }

        .header-content.hidden {
            opacity: 0;
            visibility: hidden;
            width: 0;
            margin: 0;
        }

        /* Стили для модального окна уведомлений */
        .notifications-modal {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 300px;
            max-height: 400px;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            z-index: 1100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .notifications-modal.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .notifications-header {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--gradient-1);
        }

        .notifications-content {
            max-height: 350px;
            overflow-y: auto;
            padding: 10px;
        }

        .notification-item {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: var(--gradient-1);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .notification-item:hover {
            transform: translateX(5px);
            background: var(--card-hover);
        }

        .notification-item.unread {
            border-left: 3px solid var(--accent-blue);
        }

        .notification-time {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .notification-body {
            margin-top: 5px;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .search-input.active {
                width: 150px;
            }
            
            .notifications-modal {
                width: calc(100% - 40px);
                max-height: 60vh;
            }
        }

        /* Стили для результатов поиска */
        .search-results {
            position: absolute;
            top: calc(100% + 10px); /* Отступ от поля ввода */
            right: 0;
            width: 300px;
            max-height: 400px;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            display: none;
            z-index: 1100;
            padding: 8px;
        }

        .search-result-item {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 6px;
            background: var(--gradient-1);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .search-result-item:last-child {
            margin-bottom: 0;
        }

        .search-result-item:hover {
            transform: translateX(5px);
            background: var(--card-hover);
        }

        .search-result-item img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--card-bg);
        }

        .search-result-info {
            flex: 1;
            min-width: 0; /* Для корректного переноса текста */
        }

        .search-result-name {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-result-type {
            font-size: 12px;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-no-results {
            padding: 15px;
            text-align: center;
            color: var(--text-secondary);
            background: var(--gradient-1);
            border-radius: 8px;
            margin: 8px;
        }

        /* Стили скроллбара для результатов поиска */
        .search-results::-webkit-scrollbar {
            width: 6px;
        }

        .search-results::-webkit-scrollbar-track {
            background: transparent;
        }

        .search-results::-webkit-scrollbar-thumb {
            background: var(--card-hover);
            border-radius: 3px;
        }

        /* Медиа-запросы */
        @media (max-width: 768px) {
            .search-input.active {
                width: 150px;
            }
            
            .search-results {
                width: calc(100vw - 40px);

                max-height: 60vh;
            }
        }

        /* Скрываем элементы Google Translate */
        .goog-te-banner-frame,
        .goog-te-gadget-simple,
        .goog-te-menu-value,
        .goog-te-gadget span,
        .VIpgJd-ZVi9od-l4eHX-hSRGPd,
        .skiptranslate,
        .goog-te-combo {
            display: none !important;
        }

        body {
            top: 0 !important;
        }

        .goog-te-gadget {
            height: 0;
            overflow: hidden;
        }

        iframe.skiptranslate {
            display: none !important;
        }

        .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }

        /* Стили для переключателя темы */
        .theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-1);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            
        }

        .theme-toggle i {
            position: absolute;
            transition: all 0.3s ease;
        }

        .theme-toggle .fa-sun {
            opacity: 1;
            transform: translateY(0);
        }

        .theme-toggle .fa-moon {
            opacity: 0;
            transform: translateY(20px);
        }

        /* Анимация для тёмной темы */
        .dark-theme .theme-toggle .fa-sun {
            opacity: 0;
            transform: translateY(-20px);
        }

        .dark-theme .theme-toggle .fa-moon {
            opacity: 1;
            transform: translateY(0);
        }

        /* Добавляем вращение при переключении */
        .theme-toggle.switching {
            animation: rotate 0.5s ease;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Индикатор загрузки
            const loadingIndicator = document.getElementById("loadingIndicator");

            // Показывать индикатор при загрузке страницы
            window.addEventListener("load", function () {
                loadingIndicator.classList.remove("active");
            });

            // Показать индикатор при переходах или обновлениях
            document.onreadystatechange = function () {
                if (document.readyState === "loading") {
                    loadingIndicator.classList.add("active");
                }
            };
        });
    </script>

    <!-- Google Translate Element -->
    <div id="google_translate_element" style="display: none;"></div>

    <!-- Google Translate Script -->
    <script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'ru',
            includedLanguages: 'ru,uz',
            autoDisplay: false
        }, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <script>
        // Добавляем скрипт инициализации темы до загрузки DOM
        document.documentElement.classList.add('dark-theme');
    </script>
</head>
<body class="dark-theme">



    <!-- Индикатор загрузки -->
    <div class="loading-overlay" id="loadingIndicator">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
    </div>
    <div class="container-fluid p-3 app-container">
        <div class="top-bar d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">

                <button class="btn back-btn" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div class="language-switcher">
                    <div class="notranslate lang-toggle">
                        <button class="lang-btn" data-lang="ru">RU</button>
                        <button class="lang-btn" data-lang="uz">UZ</button>
                    </div>
                </div>
                <div class="theme-switcher">
                    <div class="theme-toggle" role="button" aria-label="Переключить тему">
                        <i class="fas fa-sun"></i>
                        <i class="fas fa-moon"></i>
                    </div>
                </div>

            </div>
            <div class="profile-btn">
                <button class="btn">
                    <i class="fas fa-user-circle"></i>
                </button>
                <div class="profile-dropdown">
                    <div class="profile-dropdown-item" >
                        <i class="fas fa-user"></i>
                        Мой профиль
                    </div>
                    <div class="profile-dropdown-item">
                        <i class="fas fa-list"></i>
                        Мои объявления
                    </div>
                    <div class="profile-dropdown-divider"></div>
                   
                    <!-- <div class="profile-dropdown-item" data-translate="profile.favorites">
                        <i class="fas fa-heart"></i>
                        Избранное
                    </div> -->
                    
                    <div class="profile-dropdown-item" data-translate="profile.logout">
                        <i class="fas fa-sign-out-alt"></i>
                        Выйти
                    </div>
                </div>
            </div>
        </div>

        <header class="d-flex justify-content-between align-items-center mb-4">
            <div class="header-content">
                <img src="img/bga.png" alt="BGA" class="header-logo">
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Уведомления -->
                <div class="notifications-wrapper header-content">
                    <button href="notifications.php" 
                    class="btn notification-btn" id="notificationsBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationCount">0</span>
                    </button>
                </div>
                <!-- Поиск -->

            </div>
        </header>

        <!-- Добавить модальное окно уведомлений -->
        <div class="notifications-modal">
            <div class="notifications-header">
                <h5>Уведомлени��</h5>
            </div>
            <div class="notifications-content">
                <!-- Уведомления будут добавляться здесь -->
            </div>
        </div>

        <!-- <div class="categories mb-4">
            <div id="categories" class="d-flex gap-3">
                <?php if (!isset($_GET['page']) || $_GET['page'] === 'home'): ?>
                    
                    <button class="btn btn-dark">
                        <i class="fas fa-fire"></i> 
                        <span data-translate="popular">Популярные</span>
                    </button>
                    <button class="btn btn-dark">
                        <i class="fas fa-clock"></i> 
                        <span data-translate="new">Новые</span>
                    </button>
                    
                <?php elseif (isset($_GET['page']) && $_GET['page'] === 'freelancers'): ?>
                    
                    <select class="btn btn-dark category-select">
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
                    
                <?php elseif (isset($_GET['page']) && $_GET['page'] === 'advertisers'): ?>
                    
                    <select class="btn btn-dark category-select">
                        <option value="all" data-translate="categories.all">All Categories</option>
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
                    
                <?php elseif (isset($_GET['page']) && $_GET['page'] === 'barter'): ?>
                   
                    <select class="btn btn-dark category-select">
                        <option value="all" data-translate="categories.all">All Categories</option>
                        <option value="blogger">Bloggers</option>
                        <option value="company">Companies</option>
                        <option value="freelancer">Freelancers</option>
                    </select>
                    
                <?php else: ?>
                    
                    <select class="btn btn-dark category-select">
                        <option value="all" data-translate="categories.all">All Categories</option>
                        <option value="lifestyle">Lifestyle</option>
                        <option value="travel">Travel</option>
                        <option value="tech">Tech</option>
                        <option value="food">Food & Cooking</option>
                        <option value="beauty">Beauty & Fashion</option>
                        <option value="fitness">Fitness & Health</option>
                        <option value="gaming">Gaming</option>
                        <option value="music">Music</option>
                        <option value="business">Finance & Business</option>
                        <option value="parenting">Parenting</option>
                        <option value="education">Education</option>
                        <option value="entertainment">Entertainment</option>
                        <option value="diy">DIY & Creativity</option>
                        <option value="culture">Culture & Art</option>
                        <option value="auto">Auto & Moto</option>
                        <option value="eco">Eco & Sustainability</option>
                        <option value="social">Social & Community</option>
                        <option value="animals">Animals & Nature</option>
                        <option value="photo">Photography</option>
                        <option value="comedy">Comedy</option>
                    </select>
                <?php endif; ?>
                
                <?php if (isset($_GET['page'])): ?>
                    <button class="btn btn-dark">
                        <i class="fas fa-fire"></i> 
                        <span data-translate="popular">Популярные</span>
                    </button>
                    <button class="btn btn-dark">
                        <i class="fas fa-clock"></i> 
                        <span data-translate="new">Новые</span>
                    </button>
                <?php endif; ?>
            </div>
        </div> -->
    </div>

   

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Переключатель темы с анимацией
        const themeToggle = document.querySelector('.theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                themeToggle.classList.add('switching');
                document.body.classList.toggle('dark-theme');
                document.documentElement.classList.toggle('dark-theme');
                const isDark = document.body.classList.contains('dark-theme');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                
                setTimeout(() => {
                    themeToggle.classList.remove('switching');
                }, 500);
            });
        }

        // Переключатель языка с Google Translate
        const langButtons = document.querySelectorAll('.lang-btn');
        const currentLang = localStorage.getItem('selectedLanguage') || 'ru';

        function changeLanguage(lang) {
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                const langMap = {
                    'ru': 'ru',
                    'uz': 'uz'
                };

                select.value = langMap[lang];
                select.dispatchEvent(new Event('change'));
            }
        }

        // Устанавлиаем активную кнопку и язык
        langButtons.forEach(btn => {
            if (btn.dataset.lang === currentLang) {
                btn.classList.add('active');
            }

            btn.addEventListener('click', () => {
                langButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const newLang = btn.dataset.lang;
                localStorage.setItem('selectedLanguage', newLang);
                
                changeLanguage(newLang);
            });
        });

        // Применяем сохраненный язык при загрузке
        setTimeout(() => {
            changeLanguage(currentLang);
        }, 1000);
    });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.addEventListener("DOMContentLoaded", function() {
    // Переключатель темы
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        // Устанавливаем тёмную тему по умолчанию
        document.body.classList.add('dark-theme');

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            const newTheme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
            localStorage.setItem('theme', newTheme);
        });
    }

    
    });

    // Восстанавливаем сохраненные настройки при загрузке
    const savedLang = localStorage.getItem('selectedLanguage');
    if (savedLang) {
        // Применяем сохраненный язык ко всем элементам
        document.querySelectorAll('[data-translate]').forEach(element => {
            const key = element.getAttribute('data-translate');
            const icon = element.querySelector('i')?.outerHTML || ''; // Сохраняем иконку если она есть
            
            const keys = key.split('.');
            let translation = translations[savedLang];
            
            // Получаем значение по вложенным ключам
            for (const k of keys) {
                if (translation && translation[k]) {
                    translation = translation[k];
                } else {
                    translation = null;
                    break;
                }
            }
            
            if (translation) {
                element.innerHTML = icon + ' ' + translation; // Возвращаем иконку с переводом
            }
        });

        // Устанавливаем активную кнопку языка
        langButtons.forEach(btn => {
            btn.classList.toggle('active', btn.textContent.toLowerCase() === savedLang);
        });
    }
});
    </script>

    <!-- Подключаем скрипт поиска -->
    <script src="scripts/search.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Search();
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Обработчик для кнопки профиля
        const profileBtn = document.querySelector('.profile-btn');
        const profileDropdown = document.querySelector('.profile-dropdown');

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            // Закрытие при клике вне меню
            document.addEventListener('click', (e) => {
                if (!profileBtn.contains(e.target)) {
                    profileDropdown.classList.remove('active');
                }
            });

            // Обработчики для пунктов меню
            const menuItems = profileDropdown.querySelectorAll('.profile-dropdown-item');
            menuItems.forEach(item => {
                item.addEventListener('click', () => {
                    const text = item.textContent.trim().toLowerCase();
                    
                    // Проверяем текст пункта меню на обоих языках
                    if (text.includes('мой профиль') || text.includes('my profile')) {
                        window.location.href = 'edit-profile.php';
                    }
                    // Проверяем текст пункта меню на обоих языках
                    else if (text.includes('мои объявления') || text.includes('mening e\'lonlarim') || text.includes('my ads')) {
                        window.location.href = 'myads.php';
                    }
                    else if (text.includes('выйти') || text.includes('chiqish') || text.includes('logout')) {
                        window.location.href = 'logout.php';
                    }
                });
            });
        }
    });
    </script>

    <script>
    // Обработчик поиска
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchIcon = document.getElementById('searchIcon');
        const searchResults = document.getElementById('searchResults');
        const searchContainer = document.querySelector('.search-container');

        // Обработчик клика по иконке поиска
        searchIcon.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            searchInput.classList.toggle('active');
            searchContainer.classList.toggle('search-active');
            
            if (searchInput.classList.contains('active')) {
                searchInput.focus();
            } else {
                searchInput.value = '';
                hideResults();
            }
        });

        // Обработчик ввода в поле поиска
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => performSearch(query), 300);
            } else {
                hideResults();
            }
        });

        // Закрытие при клике вне области поиска
        document.addEventListener('click', (e) => {
            if (!searchContainer.contains(e.target)) {
                searchInput.classList.remove('active');
                searchContainer.classList.remove('search-active');
                hideResults();
            }
        });

        // Функция поиска
        async function performSearch(query) {
            try {
                const response = await fetch(`https://blogy.uz/api/ads/search?q=${encodeURIComponent(query)}`);
                if (!response.ok) throw new Error('Ошибка сети');
                
                const data = await response.json();
                displayResults(data);
            } catch (error) {
                console.error('Ошибка поиска:', error);
                showError();
            }
        }

        // Функция отображения результатов
        function displayResults(data) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'block';

            if (!data.results || data.total === 0) {
                searchResults.innerHTML = '<div class="search-no-results">Ничего не найдено</div>';
                return;
            }

            Object.entries(data.results).forEach(([type, items]) => {
                if (items && items.length > 0) {
                    items.forEach(item => {
                        const resultItem = document.createElement('div');
                        resultItem.className = 'search-result-item';
                        
                        resultItem.innerHTML = `
                            <img src="${item.data.photo_base64 || './img/noImage.jpg'}" 
                                 onerror="this.src='./img/noImage.jpg'" 
                                 alt="${item.data.name || item.data.nickname || 'Фото профиля'}">
                            <div class="search-result-info">
                                <div class="search-result-name">${item.data.name || item.data.nickname || 'Без имени'}</div>
                                <div class="search-result-type">${getTypeLabel(type)} • ${item.data.category || 'Без категории'}</div>
                            </div>
                        `;

                        resultItem.addEventListener('click', () => {
                            window.location.href = `search-results.php?q=${encodeURIComponent(searchInput.value)}&type=${type}&selected=${item.data.id}`;
                        });

                        searchResults.appendChild(resultItem);
                    });
                }
            });
        }

        function getTypeLabel(type) {
            const labels = {
                bloggers: 'Блогер',
                companies: 'Компания',
                freelancers: 'Фрила��сер'
            };
            return labels[type] || type;
        }

        function hideResults() {
            searchResults.style.display = 'none';
        }

        function showError() {
            searchResults.innerHTML = '<div class="search-no-results">Произошла ошибка при поиске</div>';
            searchResults.style.display = 'block';
        }
    });
    </script>

    <script>
        let lastScrollPosition = window.pageYOffset;
        const topBar = document.querySelector('.top-bar');
        
        window.addEventListener('scroll', () => {
            const currentScrollPosition = window.pageYOffset;
            
            // Используем классы вместо прямого изменения стилей
            if (currentScrollPosition > lastScrollPosition && currentScrollPosition > 100) {
                topBar.classList.add('hidden');
            } else {
                topBar.classList.remove('hidden');
            }
            
            lastScrollPosition = currentScrollPosition;
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Получаем chat_id из URL
        const urlParams = new URLSearchParams(window.location.search);
        const telegramChatId = urlParams.get('telegram_chat_id');
        
        // Если chat_id есть, сохраняем его в localStorage
        if (telegramChatId) {
            localStorage.setItem('telegram_chat_id', telegramChatId);
            console.log('Saved telegram_chat_id:', telegramChatId);
        }
    });
    </script>

    
</body>
</html>


