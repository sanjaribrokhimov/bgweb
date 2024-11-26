<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BGA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Google Translate Element -->
    <div id="google_translate_element" style="display: none;"></div>

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
        </div>
    </div>

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
    document.addEventListener('DOMContentLoaded', function() {
        // Переключатель темы
        const themeToggle = document.querySelector('.theme-toggle');
        if (themeToggle) {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.classList.toggle('dark-theme', savedTheme === 'dark');

            themeToggle.addEventListener('click', () => {
                themeToggle.classList.add('switching');
                document.body.classList.toggle('dark-theme');
                const isDark = document.body.classList.contains('dark-theme');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                
                // Убираем класс анимации после завершения
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
                // Маппинг языков
                const langMap = {
                    'ru': 'ru',
                    'uz': 'uz'
                };

                select.value = langMap[lang];
                select.dispatchEvent(new Event('change'));
            }
        }

        // Устанавливаем активную кнопку и язык
        langButtons.forEach(btn => {
            if (btn.dataset.lang === currentLang) {
                btn.classList.add('active');
            }

            btn.addEventListener('click', () => {
                langButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const newLang = btn.dataset.lang;
                localStorage.setItem('selectedLanguage', newLang);
                
                // Меняем язык через Google Translate
                changeLanguage(newLang);
            });
        });

        // Применяем сохраненный язык при загрузке
        setTimeout(() => {
            changeLanguage(currentLang);
        }, 1000); // Даем время для инициализации Google Translate
    });
    </script>

    <style>
    /* Скрываем все элементы Google Translate */
    .goog-te-banner-frame,
    .goog-te-gadget-simple,
    .goog-te-menu-value,
    .goog-te-gadget span,
    .VIpgJd-ZVi9od-l4eHX-hSRGPd,
    .skiptranslate,
    .goog-te-combo {
        display: none !important;
    }

    /* Убираем отступ, который добавляет Google Translate */
    body {
        top: 0 !important;
    }

    /* Скрываем иконку Google Translate */
    .goog-te-gadget {
        height: 0;
        overflow: hidden;
    }

    /* Убираем фрейм Google Translate */
    iframe.skiptranslate {
        display: none !important;
    }

    /* Убираем верхнюю панель */
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
</body>
</html>
