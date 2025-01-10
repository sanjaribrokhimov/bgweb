import os
import time
from loguru import logger
import telebot
from telebot.types import ReplyKeyboardMarkup, KeyboardButton, WebAppInfo
from dotenv import load_dotenv
import urllib.parse

# Настройка логирования
logger.add("bot.log", rotation="1 MB", level="INFO", compression="zip")

# Загрузка переменных окружения
load_dotenv()
TOKEN = os.getenv('BOT_TOKEN', '7690904808:AAEyzgbEZ3--sQ1pkJ-bFBpnDSCY2rNq9VY')

class TelegramBot:
    TEXTS = {
        'ru': {
            'welcome': r"""
👋 *Добро пожаловать в приложение Bloger Agency!*

🎯 *Что такое Bloger Agency?*
Это уникальная платформа, где вы можете:
• Разместить объявление как блогер
• Представить свою компанию
• Найти заказы как фрилансер

✨ *Преимущества нашего приложения:*
• Удобный поиск предложений и заказов
• Прямое взаимодействие между блогерами и брендами
• Безопасные сделки
• Актуальная база предложений
• Профессиональное сообщество

💡 *Как начать?*
Просто нажмите кнопку '🌐 Открыть app' ниже и откройте для себя мир новых возможностей!

🚀 Найдите именно то, что будет полезно для вашего развития.
            """,
            'subscription': r"""
❗️ *Добро пожаловать в Bloger Agency !*

🤖 Наш бот поможет вам:
• Находить заказы и предложения
• Размещать свои услуги
• Связываться с рекламодателями
• Быть в курсе новых возможностей

📢 *Для использования всех функций и получения актуальных новостей подпишитесь на наш канал:*
[@blogerAgency](https://t.me/blogerAgensy)

✅ После подписки нажмите /start для доступа к приложению.
            """,
            'help': r"""
🔍 *Как использовать бота:*
• /start - начать работу с ботом
• Нажмите '🌐 Открыть app' для доступа к приложению
• Нажмите '📞 Контакты' для связи с нами

📱 *Возможности:*
• Просмотр наших услуг
• Связь с менеджером
• Доступ к веб-приложению
            """,
            'contacts': r"""
📞 *Наши контакты:*
• Телефон: +998 97 708 78 67
• Instagram: [bloger.agency](https://www.instagram.com/bloger.agency/)
• Сайт: [bloger.agency](https://bloger.agency)

👨‍💻 Developer: [@sanjar_3210](https://t.me/sanjar_3210)
            """,
            'choose_language': "🌍 Пожалуйста, выберите язык / Iltimos, tilni tanlang:",
            'language_changed': "✅ Язык успешно изменен на русский",
            'not_subscribed': "Для доступа к функциям бота необходимо подписаться на наш канал.",
            'subscription_verified': "✅ Подписка подтверждена! Теперь вам доступны все функции бота.",
            'subscription_failed': "❌ Подписка не найдена. Пожалуйста, подпишитесь на канал и попробуйте снова."
        },
        'uz': {
            'welcome': r"""
👋 *Bloger Agency ilovasiga xush kelibsiz!*

🎯 *Bloger Agency nima?*
Bu yerda siz:
• Bloger sifatida e'lon joylashtirishingiz
• O'z kompaniyangizni taqdim etishingiz
• Frilanser sifatida buyurtmalar topishingiz
mumkin

✨ *Bizning ilovamizning afzalliklari:*
• Takliflar va buyurtmalarni qulay qidirish
• Blogerlar va brendlar o'rtasida to'g'ridan-to'g'ri aloqa
• Xavfsiz bitimlar
• Dolzarb takliflar bazasi
• Professional hamjamiyat

💡 *Qanday boshlash kerak?*
'🌐 Ilovani ochish' tugmasini bosing va o'zingiz uchun yangi imkoniyatlar yarating!

🚀 Aynan rivojlanishingiz uchun foydali bo'lgan narsalarni toping.
            """,
            'subscription': r"""
❗️ *Bloger.Agency-ga xush kelibsiz!*

🤖 Bizning bot sizga yordam beradi:
• Buyurtmalar va takliflarni topish
• O'z xizmatlaringizni joylashtirish
• Reklama beruvchilar bilan bog'lanish
• Yangi imkoniyatlardan xabardor bo'lish

📢 *Ilovadan foydalanish va dolzarb yangiliklarni olish uchun kanalimizga obuna bo'ling:*
[@blogerAgency](https://t.me/blogerAgensy)

✅ Obuna bo'lgandan so'ng ilovaga kirish uchun /start tugmasini bosing.
            """,
            'help': r"""
🔍 *Botdan qanday foydalanish:*
• /start - botni ishga tushirish
• Ilovaga kirish uchun '🌐 Ilovani ochish' tugmasini bosing
• Biz bilan bog'lanish uchun '📞 Kontaktlar' tugmasini bosing

📱 *Imkoniyatlar:*
• Xizmatlarimizni ko'rish
• Menejer bilan bog'lanish
• Veb-ilovaga kirish
            """,
            'contacts': r"""
📞 *Bizning kontaktlar:*
• Telefon: +998 97 708 78 67
• Instagram: [bloger.agency](https://www.instagram.com/bloger.agency/)
• Sayt: [bloger.agency](https://bloger.agency)

💻 Developer: [@sanjar_3210](https://t.me/sanjar_3210)
            """,
            'choose_language': "🌍 Пожалуйста, выберите язык / Iltimos, tilni tanlang:",
            'language_changed': "✅ Til muvaffaqiyatli o'zbekchaga o'zgartirildi",
            'not_subscribed': "Bot funksiyalaridan foydalanish uchun kanalimizga obuna bo'lishingiz kerak.",
            'subscription_verified': "✅ Obuna tasdiqlandi! Endi botning barcha funksiyalari sizga dostup.",
            'subscription_failed': "❌ Obuna topilmadi. Iltimos, kanalga obuna bo'ling va qaytadan urinib ko'ring."
        }
    }

    def __init__(self):
        self.bot = telebot.TeleBot(TOKEN)
        self.channel_id = "@blogerAgensy"
        self.user_languages = {}
        self.setup_handlers()
        
    def create_language_keyboard(self):
        keyboard = ReplyKeyboardMarkup(resize_keyboard=True)
        buttons = [
            KeyboardButton("🇷🇺 Русский"),
            KeyboardButton("🇺🇿 O'zbekcha")
        ]
        keyboard.add(*buttons)
        return keyboard

    def create_main_keyboard(self, lang='ru', chat_id=None):
        keyboard = ReplyKeyboardMarkup(resize_keyboard=True)
        
        try:
            if chat_id:
                user = self.bot.get_chat(chat_id)
                logger.info(f"Got user data from Telegram: username={user.username}, first_name={user.first_name}, id={user.id}")
                
                # Сначала запросим номер телефона
                contact_keyboard = ReplyKeyboardMarkup(resize_keyboard=True)
                contact_keyboard.add(KeyboardButton(
                    text="📱 Отправить номер телефона" if lang == 'ru' else "📱 Telefon raqamini yuborish",
                    request_contact=True
                ))
                
                self.bot.send_message(
                    chat_id=chat_id,
                    text="Для продолжения, пожалуйста, поделитесь номером телефона" if lang == 'ru' else 
                         "Davom etish uchun telefon raqamingizni ulashing",
                    reply_markup=contact_keyboard
                )
                
                # После получения контакта будем обрабатывать в handle_contact
                return None
                
            else:
                logger.warning("No chat_id provided")
                
        except Exception as e:
            logger.error(f"Error requesting contact: {e}", exc_info=True)
            
        return keyboard

    def handle_language_selection(self, message):
        try:
            # Определяем выбранный язык
            if message.text == "🇷🇺 Русский":
                lang = 'ru'
                response_text = "Пожалуйста, поделитесь номером телефона для продолжения"
            elif message.text == "🇺🇿 O'zbekcha":
                lang = 'uz'
                response_text = "Davom etish uchun telefon raqamingizni ulashing"
            else:
                return

            # Сохраняем выбранный язык
            self.user_languages[message.from_user.id] = lang
            logger.info(f"User {message.from_user.id} selected language: {lang}")

            # Создаем клавиатуру для запроса номера
            contact_keyboard = ReplyKeyboardMarkup(resize_keyboard=True)
            contact_keyboard.add(KeyboardButton(
                text="📱 Отправить номер телефона" if lang == 'ru' else "📱 Telefon raqamini yuborish",
                request_contact=True
            ))

            # Запрашиваем номер телефона
            self.bot.send_message(
                chat_id=message.chat.id,
                text=response_text,
                reply_markup=contact_keyboard
            )

        except Exception as e:
            logger.error(f"Error in handle_language_selection: {e}", exc_info=True)

    def handle_contact(self, message):
        try:
            if message.contact is not None:
                user = self.bot.get_chat(message.chat.id)
                phone = message.contact.phone_number
                logger.info(f"Received contact: {phone} from user {user.username}")
                
                # Получаем язык пользователя
                lang = self.user_languages.get(message.from_user.id, 'ru')
                
                # Формируем данные для URL
                init_data = {
                    'tg_chat_id': str(user.id),
                    'tg_username': str(user.username) if user.username else '',
                    'tg_user_id': str(user.id),
                    'tg_first_name': str(user.first_name) if user.first_name else '',
                    'tg_phone': str(phone),
                    'tg_auth_date': str(int(time.time()))
                }
                
                # Создаем URL с данными
                param_string = '&'.join([f"{k}={v}" for k, v in init_data.items() if v])
                encoded_data = urllib.parse.quote(param_string)
                web_app_url = f"https://8962-84-54-90-182.ngrok-free.app/login.php?tgdata={encoded_data}"
                web_app = WebAppInfo(url=web_app_url)
                
                # Создаем основную клавиатуру
                keyboard = ReplyKeyboardMarkup(resize_keyboard=True)
                buttons_text = {
                    'ru': [
                        (KeyboardButton(text="🌐 Открыть app", web_app=web_app)),
                        "👥 Наша группа", "📢 Наш канал",
                        "❓ Помощь", "📞 Контакты",
                        "🔄 Сменить язык"
                    ],
                    'uz': [
                        (KeyboardButton(text="🌐 Ilovani ochish", web_app=web_app)),
                        "👥 Bizning guruh", "📢 Bizning kanal",
                        "❓ Yordam", "📞 Kontaktlar",
                        "🔄 Tilni o'zgartirish"
                    ]
                }

                # Создаем кнопки
                buttons = []
                for text in buttons_text[lang]:
                    if isinstance(text, KeyboardButton):
                        buttons.append(text)
                    else:
                        buttons.append(KeyboardButton(text=text))

                # Размещаем кнопки
                keyboard.row(buttons[0])
                keyboard.row(buttons[1], buttons[2])
                keyboard.row(buttons[3], buttons[4])
                keyboard.row(buttons[5])

                # Отправляем приветственное сообщение с клавиатурой
                welcome_text = {
                    'ru': self.TEXTS[lang]['welcome'],
                    'uz': self.TEXTS[lang]['welcome']
                }
                
                self.bot.send_message(
                    chat_id=message.chat.id,
                    text=welcome_text[lang],
                    reply_markup=keyboard
                )
                
        except Exception as e:
            logger.error(f"Error handling contact: {e}", exc_info=True)

    def create_subscription_keyboard(self, lang='ru'):
        keyboard = ReplyKeyboardMarkup(resize_keyboard=True)
        buttons_text = {
            'ru': [
                "📢 Подписаться на канал",
                "✅ Я подписался"
            ],
            'uz': [
                "📢 Kanalga obuna bo'lish",
                "✅ Men obuna bo'ldim"
            ]
        }
        
        for text in buttons_text[lang]:
            keyboard.add(KeyboardButton(text=text))
        return keyboard

    def check_subscription(self, message):
        try:
            member = self.bot.get_chat_member(chat_id=self.channel_id, user_id=message.from_user.id)
            is_subscribed = member.status in ['member', 'administrator', 'creator']
            
            # Логируем результат проверки
            logger.info(f"Subscription check for user {message.from_user.id}: {is_subscribed}")
            
            return is_subscribed
        except telebot.apihelper.ApiException as e:
            logger.error(f"API Error in check_subscription: {e}")
            return False
        except Exception as e:
            logger.error(f"Unexpected error in check_subscription: {e}")
            return False

    def send_subscription_message(self, message):
        try:
            lang = self.user_languages.get(message.from_user.id, 'ru')
            
            # Используем существующий метод create_subscription_keyboard
            keyboard = self.create_subscription_keyboard(lang)
            
            self.bot.send_message(
                message.chat.id,
                self.TEXTS[lang]['subscription'],
                parse_mode='MarkdownV2',
                disable_web_page_preview=True,
                reply_markup=keyboard
            )
            
            logger.info(f"Subscription message sent to user {message.from_user.id}")
        except Exception as e:
            logger.error(f"Error in send_subscription_message: {e}")
            self.send_error_message(message)

    def subscription_required(self, handler):
        def wrapper(message):
            if self.check_subscription(message):
                return handler(message)
            else:
                self.send_subscription_message(message)
        return wrapper

    def send_welcome(self, message):
        try:
            # Проверяем подписку
            if not self.check_subscription(message):
                self.send_subscription_message(message)
                return

            lang = self.user_languages.get(message.from_user.id, 'ru')
            # Отправляем приветственное сообщение из TEXTS
            self.bot.send_message(
                message.chat.id,
                self.TEXTS[lang]['welcome'],
                parse_mode='MarkdownV2',
                reply_markup=self.create_language_keyboard()  # Начинаем с выбора языка
            )
            logger.info(f"Welcome message sent to user {message.from_user.id}")
        except Exception as e:
            logger.error(f"Error in send_welcome: {e}")
            self.send_error_message(message)

    def send_help(self, message):
        try:
            lang = self.user_languages.get(message.from_user.id, 'ru')
            self.bot.send_message(
                message.chat.id,
                self.TEXTS[lang]['help'],
                parse_mode='MarkdownV2'
            )
        except Exception as e:
            logger.error(f"Error in send_help: {e}")
            self.send_error_message(message)

    def send_contacts(self, message):
        try:
            lang = self.user_languages.get(message.from_user.id, 'ru')
            self.bot.send_message(
                message.chat.id,
                self.TEXTS[lang]['contacts'],
                parse_mode='MarkdownV2',
                disable_web_page_preview=True
            )
        except Exception as e:
            logger.error(f"Error in send_contacts: {e}")
            self.send_error_message(message)

    def send_error_message(self, message):
        try:
            lang = self.user_languages.get(message.from_user.id, 'ru')
            error_text = {
                'ru': "Извините, произошла ошибка. Попробуйте позже или свяжитесь с поддержкой.",
                'uz': "Kechirasiz, xatolik yuz berdi. Keyinroq urinib ko'ring yoki yordam xizmatiga murojaat qiling."
            }
            self.bot.send_message(message.chat.id, error_text[lang])
        except Exception as e:
            logger.error(f"Error in send_error_message: {e}")

    def setup_handlers(self):
        @self.bot.message_handler(commands=['start'])
        def start_handler(message):
            try:
                if not self.check_subscription(message):
                    self.send_subscription_message(message)
                    return
                    
                # При старте показываем выбор языка
                keyboard = self.create_language_keyboard()
                self.bot.send_message(
                    message.chat.id,
                    self.TEXTS['ru']['choose_language'],  # Используем текст из TEXTS
                    reply_markup=keyboard
                )
            except Exception as e:
                logger.error(f"Error in start handler: {e}")
                self.send_error_message(message)

        # Обработчик выбора языка
        @self.bot.message_handler(func=lambda message: message.text in ["🇷🇺 Русский", "🇺🇿 O'zbekcha"])
        def language_handler(message):
            self.handle_language_selection(message)

        # Обработчик получения контакта
        @self.bot.message_handler(content_types=['contact'])
        def contact_handler(message):
            self.handle_contact(message)

        # Обработчик для кнопки "Сменить язык"
        @self.bot.message_handler(func=lambda message: message.text in ["🔄 Сменить язык", "🔄 Tilni o'zgartirish"])
        def change_language(message):
            keyboard = self.create_language_keyboard()
            self.bot.send_message(
                message.chat.id,
                "🇷🇺 Выберите языкn🇺🇿 Tilni tanlang",
                reply_markup=keyboard
            )

        # Обработчик для кнопки "Наша группа"
        @self.bot.message_handler(func=lambda message: message.text in ["👥 Наша группа", "👥 Bizning guruh"])
        def send_group(message):
            lang = self.user_languages.get(message.from_user.id, 'ru')
            text = "Наша группа: https://t.me/blogerAgencyGroup" if lang == 'ru' else "Bizning guruh: https://t.me/bloger_agency_group"
            self.bot.send_message(message.chat.id, text)

        # Обработчик для кнопки "Наш канал"
        @self.bot.message_handler(func=lambda message: message.text in ["📢 Наш канал", "📢 Bizning kanal"])
        def send_channel(message):
            lang = self.user_languages.get(message.from_user.id, 'ru')
            text = "Наш канал: https://t.me/bloger_agency" if lang == 'ru' else "Bizning kanal: https://t.me/bloger_agency"
            self.bot.send_message(message.chat.id, text)

        # Обработчик для кнопки "Помощь"
        @self.bot.message_handler(func=lambda message: message.text in ["❓ Помощь", "❓ Yordam"])
        def help_handler(message):
            lang = self.user_languages.get(message.from_user.id, 'ru')
            help_text = {
                'ru': self.TEXTS[lang]['help'],
                'uz': self.TEXTS[lang]['help']
            }
            self.bot.send_message(message.chat.id, help_text[lang])

        # Обработчик для кнопки "Контакты"
        @self.bot.message_handler(func=lambda message: message.text in ["📞 Контакты", "📞 Kontaktlar"])
        def contacts_handler(message):
            lang = self.user_languages.get(message.from_user.id, 'ru')
            contacts_text = {
                'ru': self.TEXTS[lang]['contacts'],
                'uz': self.TEXTS[lang]['contacts']
            }
            self.bot.send_message(message.chat.id, contacts_text[lang])

    def run(self):
        try:
            logger.info("Бот запущен")
            self.bot.infinity_polling(timeout=10, long_polling_timeout=5)
        except KeyboardInterrupt:
            logger.info("Бот остановлен пользователем")
            self.bot.stop_polling()
        except Exception as e:
            logger.error(f"Ошибка бота: {e}")
            time.sleep(5)

if __name__ == "__main__":
    bot = TelegramBot()
    bot.run() 