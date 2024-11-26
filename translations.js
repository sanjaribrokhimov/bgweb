const translations = {
    ru: {
        search: 'Поиск...',
        all: 'Все',
        new: 'Новые',
        popular: 'Популярные',
        details: 'Подробнее',
        accept: 'Согласиться',
        cards: {
            blogger: {
                title: 'Блогер',
                followers: 'подписчиков',
                engagement: 'ER:',
                from: 'от'
            },
            campaign: {
                title: 'Рекламная кампания',
                duration: 'дней',
                from: 'от'
            },
            barter: {
                title: 'Бартер',
                requirements: 'от',
                followers: 'подписчиков'
            },
            freelancer: {
                title: 'Фрилансер',
                experience: 'Опыт:',
                from: 'от'
            }
        },
        profile: {
            myProfile: 'Мой профиль',
            settings: 'Настройки',
            myAds: 'Мои объявления',
            favorites: 'Избранное',
            logout: 'Выйти'
        },
        categories: {
            all: 'Все категории',
            barter: {
                blogger: 'Блогеры',
                company: 'Компании',
                freelancer: 'Фрилансеры'
            }
            // ... остальные категории ...
        },
        auth: {
            login: 'Вход',
            register: 'Регистрация',
            email: 'Email',
            password: 'Пароль',
            name: 'Имя',
            confirmPassword: 'Подтвердите пароль',
            rememberMe: 'Запомнить меня',
            forgotPassword: 'Забыли пароль?',
            loginButton: 'Войти',
            registerButton: 'Зарегистрироваться',
            orLoginWith: 'Или войдите через',
            selectCategory: 'Выберите категорию',
            categories: {
                blogger: 'Блогер',
                company: 'Компания',
                freelancer: 'Фрилансер'
            },
            phone: 'Номер телефона',
            otpTitle: 'Подтверждение',
            otpDescription: 'Введите код подтверждения, отправленный на ваш номер телефона',
            otpTimer: 'Повторная отправка через:',
            otpResend: 'Отправить код повторно',
            otpVerify: 'Подтвердить'
        },
        notifications: {
            success: 'Успешно подтверждено!',
            accepted: 'Отлично! Заявка принята!',
            approved: 'Предложение одобрено!'
        },
        addBlogger: {
            title: 'Добавить блогера',
            ads: 'Реклама',
            barter: 'Бартер',
            uploadPhoto: 'Загрузить фото',
            nickname: 'Никнейм',
            selectCategory: 'Выберите категорию',
            followers: 'Количество подписчиков',
            engagement: 'ER % (Вовлеченность)',
            telegramUsername: 'Username Telegram',
            instagram: {
                link: 'Ссылка на Instagram',
                postPrice: 'Цена за пост ($)',
                storyPrice: 'Цена за историю ($)',
                photoPrice: 'Цена за фото пост ($)'
            },
            telegram: {
                link: 'Ссылка на канал',
                postPrice: 'Цена за пост ($)'
            },
            youtube: {
                link: 'Ссылка на канал',
                adPrice: 'Цена за рекламу ($)'
            },
            tiktok: {
                link: 'Ссылка на профиль',
                adPrice: 'Цена за рекламу ($)'
            },
            barterConditions: 'Условия бартера',
            publish: 'Опубликовать',
            adComment: 'Комментарий к рекламе',
            socialNetworks: 'Социальные сети',
            priceSettings: 'Настройки цен',
            categories: {
                lifestyle: 'Лайфстайл',
                travel: 'Путешествия',
                tech: 'Технологии',
                food: 'Еда и кулинария',
                beauty: 'Красота и мода',
                fitness: 'Фитнес и здоровье',
                gaming: 'Игры',
                music: 'Музыка',
                business: 'Бизнес и финансы',
                education: 'Образование',
                entertainment: 'Развлечения',
                art: 'Искусство',
                sports: 'Спорт'
            }
        },
        addCompany: {
            uploadPhoto: 'Загрузить фото',
            name: 'Название компании',
            selectCategory: 'Выберите категорию',
            ads: 'Реклама',
            barter: 'Бартер',
            budget: 'Бюджет ($)',
            barterConditions: 'Условия бартера',
            duration: 'Длительность (дней)',
            website: 'Ссылка на сайт',
            instagram: 'Ссылка на Instagram',
            telegram: 'Ссылка на Telegram',
            publish: 'Опубликовать',
            adComment: 'Комментарий к рекламе'
        },
        addFreelancer: {
            uploadPhoto: 'Загрузить фото',
            name: 'Имя',
            selectCategory: 'Выберите категорию',
            price: 'Цена от ($)',
            ads: 'Реклама',
            barter: 'Бартер',
            barterConditions: 'Условия бартера',
            adComment: 'Комментарий к рекламе',
            github: 'Ссылка на GitHub',
            portfolio: 'Ссылка на портфолио',
            instagram: 'Ссылка на Instagram',
            telegram: 'Ссылка на Telegram',
            youtube: 'Ссылка на YouTube',
            publish: 'Опубликовать'
        },
        footer: {
            bloggers: 'Блогеры',
            companies: 'Компании',
            freelancers: 'Фрилансеры',
            add: 'Добавить'
        },
        myads: {
            loading: 'Загрузка...',
            noAds: 'У вас пока нет объявлений',
            deleteConfirmTitle: 'Подтверждение действия',
            deleteConfirmText: 'Вы действительно хотите удалить это объявление?',
            deleteConfirmSubtext: 'Это действие нельзя будет отменить',
            cancel: 'Отмена',
            delete: 'Удалить',
            deleteSuccess: 'Объявление успешно удалено',
            deleteError: 'Произошла ошибка при удалении объявления'
        },
        searchResults: {
            title: 'Результаты поиска',
            noResults: 'Ничего не найдено',
            details: 'Подробнее',
            category: 'Категория',
            description: 'Описание',
            noDescription: 'Описание отсутствует',
            followers: 'подписчиков',
            engagement: 'ER',
            budget: 'Бюджет',
            notSpecified: 'Не указан',
            socialNetworks: 'Социальные сети',
            website: 'Веб-сайт',
            portfolio: 'Портфолио'
        }
    },
    uz: {
        search: 'Qidirish...',
        all: 'Hammasi',
        new: 'Yangilar',
        popular: 'Mashhur',
        details: 'Batafsil',
        accept: 'Qabul qilish',
        cards: {
            blogger: {
                title: 'Bloger',
                followers: 'obunachi',
                engagement: 'ER:',
                from: 'dan'
            },
            campaign: {
                title: 'Reklama kampaniyasi',
                duration: 'kun',
                from: 'dan'
            },
            barter: {
                title: 'Barter',
                requirements: 'dan',
                followers: 'obunachi'
            },
            freelancer: {
                title: 'Frilanser',
                experience: 'Tajriba:',
                from: 'dan'
            }
        },
        profile: {
            myProfile: 'Mening profilim',
            settings: 'Sozlamalar',
            myAds: 'Mening e\'lonlarim',
            favorites: 'Sevimlilar',
            logout: 'Chiqish'
        },
        categories: {
            all: 'Barcha toifalar',
            barter: {
                blogger: 'Blogerlar',
                company: 'Kompaniyalar',
                freelancer: 'Frilanserlar'
            }
            // ... остальные категории ...
        },
        auth: {
            login: 'Kirish',
            register: 'Ro\'yxatdan o\'tish',
            email: 'Email',
            password: 'Parol',
            name: 'Ism',
            confirmPassword: 'Parolni tasdiqlang',
            rememberMe: 'Eslab qolish',
            forgotPassword: 'Parolni unutdingizmi?',
            loginButton: 'Kirish',
            registerButton: 'Ro\'yxatdan o\'tish',
            orLoginWith: 'Yoki ijtimoiy tarmoqlar orqali',
            selectCategory: 'Toifani tanlang',
            categories: {
                blogger: 'Bloger',
                company: 'Kompaniya',
                freelancer: 'Frilanser'
            },
            phone: 'Telefon raqami',
            otpTitle: 'Tasdiqlash',
            otpDescription: 'Telefon raqamingizga yuborilgan tasdiqlash kodini kiriting',
            otpTimer: 'Qayta yuborish vaqti:',
            otpResend: 'Kodni qayta yuborish',
            otpVerify: 'Tasdiqlash'
        },
        notifications: {
            success: 'Muvaffaqiyatli tasdiqlandi!',
            accepted: 'Ajoyib! Ariza qabul qilindi!',
            approved: 'Taklif tasdiqlandi!'
        },
        addBlogger: {
            title: 'Bloger qo\'shish',
            ads: 'Reklama',
            barter: 'Barter',
            uploadPhoto: 'Rasm yuklash',
            nickname: 'Taxallus',
            selectCategory: 'Toifani tanlang',
            followers: 'Obunachilar soni',
            engagement: 'ER % (Jalb qilish darajasi)',
            telegramUsername: 'Telegram username',
            instagram: {
                link: 'Instagram havolasi',
                postPrice: 'Post narxi ($)',
                storyPrice: 'Istoriya narxi ($)',
                photoPrice: 'Foto post narxi ($)'
            },
            telegram: {
                link: 'Kanal havolasi',
                postPrice: 'Post narxi ($)'
            },
            youtube: {
                link: 'YouTube kanal havolasi',
                adPrice: 'Reklama narxi ($)'
            },
            tiktok: {
                link: 'TikTok profil havolasi',
                adPrice: 'Reklama narxi ($)'
            },
            barterConditions: 'Barter shartlari',
            publish: 'E\'lon qilish',
            adComment: 'Reklama izohi',
            socialNetworks: 'Ijtimoiy tarmoqlar',
            priceSettings: 'Narx sozlamalari',
            categories: {
                lifestyle: 'Turmush tarzi',
                travel: 'Sayohat',
                tech: 'Texnologiya',
                food: 'Ovqat va oshpazlik',
                beauty: 'Go\'zallik va moda',
                fitness: 'Fitnes va salomatlik',
                gaming: 'O\'yinlar',
                music: 'Musiqa',
                business: 'Biznes va moliya',
                education: 'Ta\'lim',
                entertainment: 'Ko\'ngil ochar',
                art: 'San\'at',
                sports: 'Sport'
            }
        },
        addCompany: {
            uploadPhoto: 'Rasm yuklash',
            name: 'Kompaniya nomi',
            selectCategory: 'Toifani tanlang',
            ads: 'Reklama',
            barter: 'Barter',
            budget: 'Byudjet ($)',
            barterConditions: 'Barter shartlari',
            duration: 'Davomiyligi (kun)',
            website: 'Veb-sayt havolasi',
            instagram: 'Instagram havolasi',
            telegram: 'Telegram havolasi',
            publish: 'E\'lon qilish',
            adComment: 'Reklama izohi'
        },
        addFreelancer: {
            uploadPhoto: 'Rasm yuklash',
            name: 'Ism',
            selectCategory: 'Toifani tanlang',
            price: 'Narx ($) dan',
            ads: 'Reklama',
            barter: 'Barter',
            barterConditions: 'Barter shartlari',
            adComment: 'Reklama izohi',
            github: 'GitHub havolasi',
            portfolio: 'Portfolio havolasi',
            instagram: 'Instagram havolasi',
            telegram: 'Telegram havolasi',
            youtube: 'YouTube havolasi',
            publish: 'E\'lon qilish'
        },
        footer: {
            bloggers: 'Blogerlar',
            companies: 'Kompaniyalar',
            freelancers: 'Frilanserlar',
            add: 'Qo\'shish'
        },
        myads: {
            loading: 'Yuklanmoqda...',
            noAds: 'Sizda hali e\'lonlar yo\'q',
            deleteConfirmTitle: 'Harakatni tasdiqlash',
            deleteConfirmText: 'Haqiqatan ham bu e\'lonni o\'chirmoqchimisiz?',
            deleteConfirmSubtext: 'Bu amalni qaytarib bo\'lmaydi',
            cancel: 'Bekor qilish',
            delete: 'O\'chirish',
            deleteSuccess: 'E\'lon muvaffaqiyatli o\'chirildi',
            deleteError: 'E\'lonni o\'chirishda xatolik yuz berdi'
        },
        searchResults: {
            title: 'Qidiruv natijalari',
            noResults: 'Hech narsa topilmadi',
            details: 'Batafsil',
            category: 'Toifa',
            description: 'Tavsif',
            noDescription: 'Tavsif mavjud emas',
            followers: 'obunachilar',
            engagement: 'ER',
            budget: 'Byudjet',
            notSpecified: 'Ko\'rsatilmagan',
            socialNetworks: 'Ijtimoiy tarmoqlar',
            website: 'Veb-sayt',
            portfolio: 'Portfolio'
        }
    }
}; 