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
    display: flex;
    flex-direction: column;
    visibility: hidden;
    opacity: 0;
    /* display: none; */
    background: linear-gradient(to bottom, var(--accent-orange) 45px, var(--card-bg) 45px, var(--card-bg));
    border-radius: 12px;
    border-bottom-right-radius: 0px;
    padding: 15px;
    padding-bottom: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    transform: translate(-50%, -200%);
    top: 0%;
    left: 50%;
    width: 80%;
    max-width: 400px;
    transition: all 0.3s ease;
}


.confirm-modal.active {
    /* display: flex;
    flex-direction: column; */
    visibility: visible;
    opacity: 1;
    gap: 15px;
    transform: translate(-50%, 100%);

}

body.add-active {
    filter: blur(1px);
    transform: transalteZ(200px)
}
.confirm-content {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    position: absolute;
    top: 99%;
    right: 0;
    background: var(--card-bg);
    padding: 0 15px 15px 0;
    border-radius: 0 0 12px 12px;
}


.confirm-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 50%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    transform: skewX(45deg);
    transform-origin: bottom left;
    z-index: -1;
    border-radius: 0 0 12px 12px;
    border-bottom-right-radius: 0px;
    background: var(--card-bg);
}


.user-message {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    color: var(--text-color);
    font-size: 14px;
    outline: none;
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
    0%, 100% { transform: translate(-50%, 100%) rotate(0deg); }
    25% { transform: translate(-50%, 100%) rotate(-5deg); }
    75% { transform: translate(-50%, 100%) rotate(5deg); }
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

/* Добавляем оверлей для затемнения */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    visibility: hidden;
    opacity: 0;
    transition: all 0.3s ease;
    backdrop-filter: blur(3px);
    z-index: 999;
}

.modal-overlay.active {
    visibility: visible;
    opacity: 1;
}
</style>

<!-- Добавляем оверлей перед модальным окном -->
<div class="modal-overlay"></div>

<div class="confirm-modal">
    <div class="message-container">
        <p style="font-size: 12px; text-align: center;">Напишите сообщение пользователю</p>
        <textarea rows="3" type="text" class="user-message" placeholder="Сообщение пользователю" style="resize: none;"></textarea>
    </div>
    <div class="confirm-content">
        <button class="confirm-no">
            <i class="fas fa-times"></i>
        </button>
        <button class="confirm-yes">
            <i class="fas fa-check"></i>
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
    const requiredFields = [
        'userId', 
        // 'telegram_chat_id',
        // 'category', 
        // 'userEmail',
        // 'name',
        // 'phone',
        // 'telegram',
        // 'direction'
    ];
    const missingFields = requiredFields.filter(field => !localStorage.getItem(field));
    const isVerified = localStorage.getItem('verified');
    const currentLang = localStorage.getItem('selectedLanguage') || 'ru';
    const t = translations[currentLang];
    
    if (!window.location.pathname.includes('login.php')) {
        if (missingFields.length > 0 || isVerified === 'false') {
            console.log('Missing fields:', missingFields);
            localStorage.clear();
            window.location.href = 'login.php';
            return false;
        }
    }
    return true;
}

async function checkUser(){
    var userData;
    try {
        const userId = localStorage.getItem('userId');
        const response = await fetch(`http://localhost:8888/api/auth/check-fields/${userId}`);
        
        if (!response.ok) {
            throw new Error('Ошибка получения данных пользователя');
        }

        userData = await response.json();
        console.log('Полученные данные:', userData); // Для отладки
        
        const is_complete = userData.is_complete;
        if(!is_complete){
            window.location.href = 'reRegister.php';
        }
        const user = userData.user;
        localStorage.setItem('userId', user.id);
        // localStorage.setItem('userCategory', user.category);
        // localStorage.setItem('userEmail', user.email);
        // localStorage.setItem('userName', user.name);
        // localStorage.setItem('userPhone', user.phone);
        // localStorage.setItem('userTelegram', user.telegram);
        // localStorage.setItem('userDirection', user.direction);
        localStorage.setItem('is_complete', userData.is_complete);
        console.log('asdasd')
    } catch (error) {
        console.error('Ошибка:', error);
    }
    return userData.is_complete;
}

document.addEventListener('DOMContentLoaded', function() {
    // checkAuth();
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
        addButton.addEventListener('click', async function() {
            const userCategory = localStorage.getItem('category');
            const isVerified = localStorage.getItem('verified');
            const userId = localStorage.getItem('userId');
            // const userEmail = localStorage.getItem('userEmail');
            // const userName = localStorage.getItem('name');
            // const userPhone = localStorage.getItem('phone');
            // const userTelegram = localStorage.getItem('telegram');
            // const userDirection = localStorage.getItem('direction');
            // const userInstagram = localStorage.getItem('instagram');
            
            if (!userId) {
                window.location.href = 'login.php';
                return;
            }

            if (isVerified !== 'true') {
               
                window.location.href = 'reRegister.php';
                return;
            }
            var is_complete = await checkUser();
            console.log(is_complete);
            if(!is_complete){
                window.location.href = 'reRegister.php';
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
