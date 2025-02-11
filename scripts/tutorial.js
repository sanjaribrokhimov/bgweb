class Tutorial {
    constructor() {
        this.currentStep = 0;
        this.steps = [
            {
                element: 'body',
                title: 'Добро пожаловать!',
                text: 'Привет! Я ваш проводник по платформе. Сейчас я покажу вам основные функции и возможности сервиса. Нажмите в любое место, чтобы продолжить.',
                position: 'center',
                borderRadius: '0'
            },
            {
                element: '.profile-btn',
                title: 'Профиль',
                text: 'Нажмите сюда, чтобы перейти в свой профиль, редактировать его и просматривать свои объявления',
                position: 'top',
                borderRadius: '50%'
            },
            {
                element: '.bottom-nav',
                title: 'Навигация',
                text: 'Здесь вы можете переключаться между разделами: блогеры, компании и фрилансеры',
                position: 'top',
                borderRadius: '20px 20px 0 0'
            },
            {
                element: '#addButton',
                title: 'Создание объявления',
                text: 'Нажмите сюда, чтобы создать своё объявление',
                position: 'top',
                borderRadius: '8px'
            },
            {
                element: '.filter-container',
                title: 'Фильтрация',
                text: 'Используйте фильтры для поиска нужных предложений',
                position: 'bottom',
                borderRadius: '8px'
            },
            {
                element: '.btn-accept',
                title: 'Взаимодействие',
                text: 'Нажмите на галочку, чтобы отправить запрос на сотрудничество',
                position: 'left',
                borderRadius: '50%'
            },
            {
                element: '.btn-details',
                title: 'Подробнее',
                text: 'Нажмите сюда, чтобы перейти на страницу с подробным описанием объявления',
                position: 'bottom',
                borderRadius: '8px'
            },
            {
                element: 'body',
                title: 'Спасибо за просмотр!',
                text: 'Надеюсь, вы нашли что-то интересное! Если у вас возникли вопросы, не стесняйтесь обращаться в техподдержку.',
                position: 'center',
                borderRadius: '0'
            }
        ];

        this.init();
    }

    init() {
        // Проверяем, показывали ли уже туториал
        if (localStorage.getItem('tutorialShown')) {
            return;
        }

        this.createStyles();
        this.createTutorialElement();
        this.showStep(0);
        
        // Блокируем скролл
        document.body.style.overflow = 'hidden';
        
        // Добавляем обработчик клика для перехода к следующему шагу
        document.addEventListener('click', this.handleClick.bind(this));
    }

    createStyles() {
        const style = document.createElement('style');
        style.textContent = `
        *
        {
            transition: .2s;
        }
            .tutorial-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 9998;
            }

            .tutorial-highlight {
                position: absolute;
                background: rgba(255, 255, 255, 0.1);
                box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.85);
                z-index: 9999;
                pointer-events: none;
            }

            .tutorial-popup {
                position: absolute;
                background: var(--card-bg);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                padding: 20px 20px 20px 50px;
                width: 280px;
                z-index: 10000;
                animation: tutorialFadeIn 0.3s ease;
            }

            .tutorial-character {
                position: absolute;
                left: -15px;
                top: -15px;
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--accent-blue);
                border-radius: 50%;
                box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            }

            .character {
                font-size: 25px;
                line-height: 1;
            }

            .tutorial-content {
                position: relative;
            }

            .tutorial-content:before {
                content: '';
                position: absolute;
                left: -25px;
                top: 15px;
                width: 10px;
                height: 10px;
                background: var(--accent-blue);
                transform: rotate(45deg);
            }

            .tutorial-popup h3 {
                color: var(--accent-blue);
                margin: 0 0 10px 0;
                font-size: 18px;
            }

            .tutorial-popup p {
                color: var(--text-color);
                margin: 0;
                font-size: 14px;
                line-height: 1.5;
            }

            .tutorial-buttons {
                display: flex;
                justify-content: space-between;
                gap: 10px;
            }

            .tutorial-button {
                padding: 8px 15px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 14px;
            }

            .tutorial-next {
                background: var(--accent-blue);
                color: white;
            }

            .tutorial-skip {
                background: rgba(255, 255, 255, 0.1);
                color: var(--text-color);
            }

            .tutorial-button:hover {
                transform: translateY(-2px);
            }

            @keyframes tutorialFadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .tutorial-confirm-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                z-index: 10001;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .tutorial-confirm-dialog {
                background: var(--card-bg);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 25px;
                width: 90%;
                max-width: 400px;
                animation: tutorialFadeIn 0.3s ease;
            }

            .tutorial-confirm-dialog h3 {
                color: var(--accent-blue);
                margin: 0 0 15px 0;
                font-size: 20px;
                text-align: center;
            }

            .tutorial-confirm-dialog p {
                color: var(--text-color);
                margin: 0 0 20px 0;
                font-size: 15px;
                line-height: 1.5;
                text-align: center;
            }

            .tutorial-confirm-buttons {
                display: flex;
                justify-content: center;
                gap: 15px;
            }

            .tutorial-confirm-button {
                padding: 12px 25px;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 15px;
                min-width: 120px;
            }

            .tutorial-confirm-yes {
                background: var(--accent-blue);
                color: white;
            }

            .tutorial-confirm-no {
                background: rgba(255, 255, 255, 0.1);
                color: var(--text-color);
            }

            .tutorial-confirm-button:hover {
                transform: translateY(-2px);
            }
        `;
        document.head.appendChild(style);
    }

    createTutorialElement() {
        const overlay = document.createElement('div');
        overlay.className = 'tutorial-overlay';
        document.body.appendChild(overlay);

        const highlight = document.createElement('div');
        highlight.className = 'tutorial-highlight';
        document.body.appendChild(highlight);

        const popup = document.createElement('div');
        popup.className = 'tutorial-popup';
        document.body.appendChild(popup);

        this.overlay = overlay;
        this.highlight = highlight;
        this.popup = popup;
    }

    handleClick(event) {
        // Если клик был по попапу, игнорируем его
        if (event.target.closest('.tutorial-popup')) {
            return;
        }

        // Переходим к следующему шагу
        this.showStep(this.currentStep + 1);
    }

    showStep(index) {
        this.currentStep = index;
        
        if (index >= this.steps.length) {
            this.finish();
            return;
        }

        const step = this.steps[index];
        const element = document.querySelector(step.element);
        
        if (!element) {
            this.showStep(index + 1);
            return;
        }

        // Для первого шага - центрируем попап
        if (index === 0 || index === 7) {
            const viewportHeight = window.innerHeight;
            const viewportWidth = window.innerWidth;
            
            this.highlight.style.display = 'block';
            this.highlight.style.background = 'rgba(0, 0, 0, 0.85)';
            
            this.popup.style.top = `${(viewportHeight - 200) / 2}px`;
            this.popup.style.left = `${(viewportWidth - 280) / 2}px`;
            
            // Специальный стиль для приветственного попапа
            this.popup.style.padding = '25px 25px 25px 60px';
            this.popup.style.width = '320px';
            
            this.popup.innerHTML = `
                <div class="tutorial-character">
                    <div class="character">👋</div>
                </div>
                <div class="tutorial-content">
                    <h3>${step.title}</h3>
                    <p>${step.text}</p>
                </div>
            `;
            return;
        }else   
        {
            this.highlight.style.background = 'rgba(255, 255, 255, 0.1)';
        }

        // Возвращаем стандартные стили для остальных шагов
        this.highlight.style.display = 'block';
        this.popup.style.padding = '20px 20px 20px 50px';
        this.popup.style.width = '280px';

        const rect = element.getBoundingClientRect();

        if(index === 5 || index === 6)
        {
            console.log(rect.top)
            console.log(document.querySelector('.bottom-nav').getBoundingClientRect().top)
            if(element.offsetTop > document.querySelector('.bottom-nav').offsetTop)
            {
                window.scrollTo({
                    top: element.offsetTop - document.querySelector('.bottom-nav').offsetTop,
                    behavior: 'smooth'
                });
                
            }

            this.highlight.style.top = `${element.offsetTop}px`;

        }else{
            this.highlight.style.top = `${rect.top}px`; 
        }
        
        // Подсветка элемента
        this.highlight.style.left = `${rect.left}px`;
        this.highlight.style.width = `${rect.width}px`;
        this.highlight.style.height = `${rect.height}px`;
        this.highlight.style.borderRadius = step.borderRadius;


        // Позиционирование попапа
        const popupHeight = 200;
        const margin = 20;
        let popupTop;
        if (rect.top > popupHeight + margin) {
            popupTop = rect.top - popupHeight - margin;
        } else {
            popupTop = rect.bottom + margin;
        }

        let popupLeft = rect.left + (rect.width - 280) / 2;

        if (popupLeft < margin) {
            popupLeft = margin;
        } else if (popupLeft + 280 > window.innerWidth - margin) {
            popupLeft = window.innerWidth - 280 - margin;
        }

        this.popup.style.top = `${popupTop}px`;
        this.popup.style.left = `${popupLeft}px`;

        // Содержимое попапа с персонажем
        this.popup.innerHTML = `
            <div class="tutorial-character">
                <div class="character">
                    <img src="/img/tutorialer.png" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
            <div class="tutorial-content">
                <h3>${step.title}</h3>
                <p>${step.text}</p>
            </div>
        `;
    }

    finish() {
        window.scroll(0, 0)
        // Удаляем обработчик клика
        document.removeEventListener('click', this.handleClick.bind(this));
        
        // Разблокируем скролл
        document.body.style.overflow = '';
        
        this.overlay.remove();
        this.highlight.remove();
        this.popup.remove();
        
        localStorage.setItem('tutorialShown', 'true');
    }

    restart() {
        localStorage.removeItem('tutorialShown');
        this.createStyles();
        this.createTutorialElement();
        this.showStep(0);
    }

    showConfirmDialog() {
        const overlay = document.createElement('div');
        overlay.className = 'tutorial-confirm-overlay';
        
        overlay.innerHTML = `
            <div class="tutorial-confirm-dialog">
                <h3>Добро пожаловать!</h3>
                <p>Хотите пройти краткое обучение по использованию платформы?</p>
                <div class="tutorial-confirm-buttons">
                    <button class="tutorial-confirm-button tutorial-confirm-no" onclick="tutorial.handleConfirm(false)">
                        Нет, спасибо
                    </button>
                    <button class="tutorial-confirm-button tutorial-confirm-yes" onclick="tutorial.handleConfirm(true)">
                        Да, покажите
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);
    }

    handleConfirm(confirmed) {
        // Удаляем диалог подтверждения
        document.querySelector('.tutorial-confirm-overlay').remove();

        if (confirmed) {
            // Если пользователь согласился, начинаем туториал
            this.createTutorialElement();
            this.showStep(0);
        }

        // В любом случае отмечаем, что туториал был предложен
        localStorage.setItem('tutorialShown', 'true');
    }

    // createRestartButton() {
    //     const button = document.createElement('button');
    //     button.className = 'tutorial-restart-button';
    //     button.innerHTML = '<i class="fas fa-question-circle"></i>';
    //     button.onclick = () => this.restart();
    //     document.body.appendChild(button);
    // }
}

// Инициализация туториала при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    window.tutorial = new Tutorial();
}); 