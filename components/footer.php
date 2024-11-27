<nav class="bottom-nav">
    <div class="d-flex justify-content-around ">
        <a href="index.php?page=bloggers" class="btn btn-link nav-item" data-page="bloggers">
            <div class="nav-icon">
                <i class="fas fa-user"></i>
                <span class="nav-label" data-translate="footer.bloggers">Блогеры</span>
            </div>
        </a>
        <a href="index.php?page=advertisers" class="btn btn-link nav-item" data-page="advertisers">
            <div class="nav-icon">
                <i class="fas fa-chart-line"></i>
                <span class="nav-label" data-translate="footer.companies">Компании</span>
            </div>
        </a>
        <a href="index.php?page=freelancers" class="btn btn-link nav-item" data-page="freelancers">
            <div class="nav-icon">
                <i class="fas fa-laptop-code"></i>
                <span class="nav-label" data-translate="footer.freelancers">Фрилансеры</span>
            </div>
        </a>
        <button class="btn btn-link nav-item" id="addButton">
            <div class="nav-icon">
                <i class="fas fa-plus-circle"></i>
                <span class="nav-label" data-translate="footer.add">Добавить</span>
            </div>
        </button>
    </div>
</nav>

<style>
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--card-bg);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    transition: transform 0.3s ease;
    z-index: 1000;
    padding: 10px 0;
}

.bottom-nav.hidden {
    transform: translateY(100%);
}

.nav-item {
    text-decoration: none;
    color: var(--text-secondary);
    transition: all 0.3s ease;
    padding: 4px 12px;
    border-radius: 12px;
}

.nav-item:hover {
    color: var(--text-color);
    background: var(--card-hover);
    transform: translateY(-2px);
}

.nav-item.active {
    color: var(--accent-blue);
    background: var(--card-hover);
}

.nav-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.nav-icon i {
    font-size: 18px;
    margin-bottom: 1px;
}

.nav-label {
    font-size: 11px;
    text-align: center;
    white-space: nowrap;
}

/* Остальные стили остаются без изменений */
.confirm-modal {
    position: fixed;
    display: none;
    background: var(--card-bg);
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    transform: translate(-50%, -50%);
}

.confirm-modal.active {
    display: flex;
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

.confirm-modal.shake {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
    25% { transform: translate(-50%, -50%) rotate(-5deg); }
    75% { transform: translate(-50%, -50%) rotate(5deg); }
}

/* Стили для уведомлений */
.notification-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    background: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    gap: 10px;
    transform: translateX(120%);
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 1100;
}

.notification-toast.show {
    transform: translateX(0);
    opacity: 1;
}
</style>

<!-- Остальной код остается без изменений -->
<!-- Модальные окна и скрипты -->
<div class="confirm-modal">
    <div class="confirm-content">
        <button class="confirm-yes">
            <i class="fas fa-check"></i>
        </button>
        <button class="confirm-no">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Уведомления -->
<div class="notification top" style="display: none;" data-translate="notifications.success">Успешно подтверждено!</div>
<div class="notification top-right" style="display: none;" data-translate="notifications.accepted">Отлично! Заявка принята!</div>
<div class="notification top-left" style="display: none;" data-translate="notifications.approved">Предложение одобрено!</div>

<!-- Модальное окно для подробной информации -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подробная информация</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Контент будет добавлен динамически -->
            </div>
        </div>
    </div>
</div>

<script>
// Функция проверки авторизации
function checkAuth() {
    const requiredFields = ['userId', 'category', 'userEmail'];
    const missingFields = requiredFields.filter(field => !localStorage.getItem(field));
    const isVerified = localStorage.getItem('verified');
    const currentLang = localStorage.getItem('selectedLanguage') || 'ru';
    const t = translations[currentLang];
    
    if (!window.location.pathname.includes('login.php')) {
        if (missingFields.length > 0 || isVerified === 'false') {
            alert(t.auth.needAuth);
            localStorage.clear();
            window.location.href = 'login.php';
            return false;
        }
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    setInterval(checkAuth, 30000);
    
    // Функция обновления переводов футера
    function updateFooterTranslations() {
        const currentLang = localStorage.getItem('selectedLanguage') || 'ru';
        const t = translations[currentLang];
        
        // Обновляем все элементы с data-translate в футере
        document.querySelectorAll('.bottom-nav [data-translate]').forEach(element => {
            const key = element.getAttribute('data-translate');
            const translationKey = key.split('.')[1];
            if (t && t.footer && t.footer[translationKey]) {
                element.textContent = t.footer[translationKey];
            }
        });
    }

    // Обновляем переводы при загрузке
    updateFooterTranslations();

    // Слушаем клики на кнопках переключения языка
    const langButtons = document.querySelectorAll('.lang-toggle button');
    langButtons.forEach(button => {
        button.addEventListener('click', () => {
            setTimeout(updateFooterTranslations, 0); // Добавляем небольшую задержку
        });
    });

    // Также оставляем слушатель storage для синхронизации между вкладками
    window.addEventListener('storage', function(e) {
        if (e.key === 'selectedLanguage') {
            updateFooterTranslations();
        }
    });

    // Обработчик кнопки добавления
    const addButton = document.getElementById('addButton');
    if (addButton) {
        addButton.addEventListener('click', function() {
            const userCategory = localStorage.getItem('category');
            const isVerified = localStorage.getItem('verified');
            const userId = localStorage.getItem('userId');
            const userEmail = localStorage.getItem('userEmail');
            const currentLang = localStorage.getItem('selectedLanguage') || 'ru';
            const t = translations[currentLang];
            
            if (!userCategory || !userId || !userEmail) {
                alert(t.auth.needAuth);
                window.location.href = 'login.php';
                return;
            }

            if (isVerified !== 'true') {
                alert(t.auth.verifyAccount);
                window.location.href = 'verification.php';
                return;
            }

            const redirectPages = {
                blogger: 'add_blogger.php',
                company: 'add_company.php',
                freelancer: 'add_freelancer.php'
            };

            const redirectPage = redirectPages[userCategory];
            if (redirectPage) {
                window.location.href = redirectPage;
            } else {
                alert(t.auth.unknownCategory);
            }
        });
    }

    // Обновляем активную кнопку в навигации
    const navButtons = document.querySelectorAll('.bottom-nav a');
    const currentPage = new URLSearchParams(window.location.search).get('page') || 'home';
    navButtons.forEach(button => {
        if (button.getAttribute('data-page') === currentPage) {
            button.classList.add('active');
        }
    });
});

let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {
    const bottomNav = document.querySelector('.bottom-nav');
    const currentScrollY = window.scrollY;
    
    // Скрываем при скролле вниз, показываем при скролле вверх
    if (currentScrollY > lastScrollY) {
        bottomNav.classList.add('hidden');
    } else {
        bottomNav.classList.remove('hidden');
    }
    
    lastScrollY = currentScrollY;
});
</script>
