import os
import time
from loguru import logger
import telebot
from telebot.types import ReplyKeyboardMarkup, KeyboardButton, WebAppInfo
from dotenv import load_dotenv

# Настройка логирования
logger.add("bot.log", rotation="1 MB", level="INFO", compression="zip")

# Загрузка переменных окружения
load_dotenv()
TOKEN = os.getenv('BOT_TOKEN', '7690904808:AAEyzgbEZ3--sQ1pkJ-bFBpnDSCY2rNq9VY')

class TelegramBot:
    TEXTS = {
        'ru': {
            'welcome': r"""
👋 *Добро пожаловать в приложение Bloger\.Agensy\!*

🎯 *Что такое Bloger\.Agensy?*
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
Просто нажмите кнопку '🌐 Открыть app' ниже и откройте для себя мир новых возможностей\!

🚀 Найдите именно то, что будет полезно для вашего развития\.
            """,
            'subscription': r"""
❗️ *Добро пожаловать в Bloger\.Agensy\!*

🤖 Наш бот поможет вам:
• Находить заказы и предложения
• Размещать свои услуги
• Связываться с рекламодателями
• Быть в курсе новых возможностей

📢 *Для использования всех функций и получения актуальных новостей подпишитесь на наш канал:*
[@blogerAgensy](https://t\.me/blogerAgensy)

✅ После подписки нажмите /start для доступа к приложению\.
            """,
            'help': r"""
🔍 *Как использовать бота:*
• /start \- начать работу с ботом
• Нажмите '🌐 Открыть app' для доступа к приложению
• Нажмите '📞 Контакты' для связи с нами

📱 *Возможности:*
• Просмотр наших услуг
• Связь с менеджером
• Доступ к веб\-приложению
            """,
            'contacts': r"""
📞 *Наши контакты:*
• Телефон: \+998 97 708 78 67
• Instagram: [bloger\.agency](https://www\.instagram\.com/bloger\.agency/)
• Сайт: [bloger\.agency](https://bloger\.agency)

👨‍💻 Developer: [@sanjar\_3210](https://t\.me/sanjar\_3210)
            """,
            'choose_language': "🌍 Пожалуйста, выберите язык / Iltimos, tilni tanlang:",
            'language_changed': "✅ Язык успешно изменен на русский"
        },
        'uz': {
            'welcome': r"""
👋 *Bloger\.Agensy ilovasiga xush kelibsiz\!*

🎯 *Bloger\.Agensy nima?*
Bu yerda siz:
• Bloger sifatida e'lon joylashtirishingiz
• O'z kompaniyangizni taqdim etishingiz
• Frilanser sifatida buyurtmalar topishingiz
mumkin

✨ *Bizning ilovamizning afzalliklari:*
• Takliflar va buyurtmalarni qulay qidirish
• Blogerlar va brendlar o'rtasida to'g'ridan\-to'g'ri aloqa
• Xavfsiz bitimlar
• Dolzarb takliflar bazasi
• Professional hamjamiyat

💡 *Qanday boshlash kerak?*
'🌐 Ilovani ochish' tugmasini bosing va o'zingiz uchun yangi imkoniyatlar yarating\!

🚀 Aynan rivojlanishingiz uchun foydali bo'lgan narsalarni toping\.
            """,
            'subscription': r"""
❗️ *Bloger\.Agensy\-ga xush kelibsiz\!*

🤖 Bizning bot sizga yordam beradi:
• Buyurtmalar va takliflarni topish
• O'z xizmatlaringizni joylashtirish
• Reklama beruvchilar bilan bog'lanish
• Yangi imkoniyatlardan xabardor bo'lish

📢 *Ilovadan foydalanish va dolzarb yangiliklarni olish uchun kanalimizga obuna bo'ling:*
[@blogerAgensy](https://t\.me/blogerAgensy)

✅ Obuna bo'lgandan so'ng ilovaga kirish uchun /start tugmasini bosing\.
            """,
            'help': r"""
🔍 *Botdan qanday foydalanish:*
• /start \- botni ishga tushirish
• Ilovaga kirish uchun '🌐 Ilovani ochish' tugmasini bosing
• Biz bilan bog'lanish uchun '📞 Kontaktlar' tugmasini bosing

📱 *Imkoniyatlar:*
• Xizmatlarimizni ko'rish
• Menejer bilan bog'lanish
• Veb\-ilovaga kirish
            """,
            'contacts': r"""
📞 *Bizning kontaktlar:*
• Telefon: \+998 97 708 78 67
• Instagram: [bloger\.agency](https://www\.instagram\.com/bloger\.agency/)
• Sayt: [bloger\.agency](https://bloger\.agency)

💻 Developer: [@sanjar\_3210](https://t\.me/sanjar\_3210)
            """,
            'choose_language': "🌍 Пожалуйста, выберите язык / Iltimos, tilni tanlang:",
            'language_changed': "✅ Til muvaffaqiyatli o'zbekchaga o'zgartirildi"
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
        # Используем chat_id из параметра если он передан
        web_app_url = f"https://bgweb.nurali.uz/index.php"
        if chat_id:
            web_app_url += f"?chat_id={chat_id}"
        web_app = WebAppInfo(url=web_app_url)
        
        buttons_text = {
            'ru': [
                ("🌐 Открыть app", web_app),
                "👥 Наша группа",
                "📢 Наш канал",
                "❓ Помощь",
                "📞 Контакты",
                "🔄 Сменить язык"
            ],
            'uz': [
                ("🌐 Ilovani ochish", web_app),
                "👥 Bizning guruh",
                "📢 Bizning kanal",
                "❓ Yordam",
                "📞 Kontaktlar",
                "🔄 Tilni o'zgartirish"
            ]
        }
        
        buttons = []
        for text in buttons_text[lang]:
            if isinstance(text, tuple):
                buttons.append(KeyboardButton(text=text[0], web_app=text[1]))
            else:
                buttons.append(KeyboardButton(text=text))

        keyboard.row(buttons[0])
        keyboard.row(buttons[1], buttons[2])
        keyboard.row(buttons[3], buttons[4])
        keyboard.row(buttons[5])
        return keyboard

    def check_subscription(self, message):
        try:
            member = self.bot.get_chat_member(chat_id=self.channel_id, user_id=message.from_user.id)
            return member.status in ['member', 'administrator', 'creator']
        except telebot.apihelper.ApiException as e:
            logger.error(f"API Error in check_subscription: {e}")
            # В случае ошибки доступа к списку частников, 
            # временно пропускаем проверку подписки
            return True
        except Exception as e:
            logger.error(f"Error checking subscription: {e}")
            return False

    def send_subscription_message(self, message):
        try:
            lang = self.user_languages.get(message.from_user.id, 'ru')
            self.bot.send_message(
                message.chat.id,
                self.TEXTS[lang]['subscription'],
                parse_mode='MarkdownV2',
                disable_web_page_preview=True
            )
        except Exception as e:
            logger.error(f"Error in send_subscription_message: {e}")

    def subscription_required(self, handler):
        def wrapper(message):
            if self.check_subscription(message):
                return handler(message)
            else:
                self.send_subscription_message(message)
        return wrapper

    def send_welcome(self, message):
        try:
            lang = self.user_languages.get(message.from_user.id, 'ru')
            with open('img/logo.png', 'rb') as photo:
                self.bot.send_photo(
                    message.chat.id,
                    photo,
                    caption=self.TEXTS[lang]['welcome'],
                    parse_mode='MarkdownV2',
                    reply_markup=self.create_main_keyboard(lang)
                )
            logger.info(f"Welcome message with logo sent to user {message.from_user.id}")
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
        def start(message):
            # При первом запуске показываем выбор языка
            if message.from_user.id not in self.user_languages:
                self.bot.send_message(
                    message.chat.id,
                    self.TEXTS['ru']['choose_language'],
                    reply_markup=self.create_language_keyboard()
                )
            else:
                if self.check_subscription(message):
                    self.send_welcome(message)
                else:
                    self.send_subscription_message(message)

        @self.bot.message_handler(func=lambda message: message.text in ["🇷🇺 Русский", "🇺🇿 O'zbekcha"])
        def language_choice(message):
            user_id = message.from_user.id
            if message.text == "🇷🇺 Русский":
                self.user_languages[user_id] = 'ru'
                self.bot.send_message(
                    message.chat.id,
                    self.TEXTS['ru']['language_changed'],
                    reply_markup=self.create_main_keyboard('ru', message.chat.id)
                )
            else:
                self.user_languages[user_id] = 'uz'
                self.bot.send_message(
                    message.chat.id,
                    self.TEXTS['uz']['language_changed'],
                    reply_markup=self.create_main_keyboard('uz', message.chat.id)
                )
            
            if self.check_subscription(message):
                self.send_welcome(message)
            else:
                self.send_subscription_message(message)

        @self.bot.message_handler(func=lambda message: message.text in ["🔄 Сменить язык", "🔄 Tilni o'zgartirish"])
        def change_language(message):
            self.bot.send_message(
                message.chat.id,
                self.TEXTS['ru']['choose_language'],
                reply_markup=self.create_language_keyboard()
            )

        # Обновляем остальные обработчики с учетом языка
        @self.bot.message_handler(commands=['help'])
        def help(message):
            if self.check_subscription(message):
                self.send_help(message)
            else:
                self.send_subscription_message(message)

        @self.bot.message_handler(func=lambda message: message.text in ["❓ Помощь", "❓ Yordam"])
        def help_button(message):
            if self.check_subscription(message):
                self.send_help(message)
            else:
                self.send_subscription_message(message)

        @self.bot.message_handler(func=lambda message: message.text in ["📞 Контакты", "📞 Kontaktlar"])
        def contacts_button(message):
            if self.check_subscription(message):
                self.send_contacts(message)
            else:
                self.send_subscription_message(message)

        @self.bot.message_handler(func=lambda message: message.text in ["👥 Наша группа", "👥 Bizning guruh"])
        def group_button(message):
            if self.check_subscription(message):
                lang = self.user_languages.get(message.from_user.id, 'ru')
                try:
                    group_text = {
                        'ru': "💬 Присоединяйтесь к нашей группе для общения: [Bloger\\.Agensy Group](https://t.me/blogerAgencygroup)",
                        'uz': "💬 Muloqot uchun guruhimizga qo'shiling: [Bloger\\.Agensy Group](https://t.me/blogerAgencygroup)"
                    }
                    self.bot.send_message(
                        message.chat.id,
                        group_text[lang],
                        parse_mode='MarkdownV2',
                        disable_web_page_preview=True
                    )
                except Exception as e:
                    logger.error(f"Error in group_button: {e}")
                    self.send_error_message(message)
            else:
                self.send_subscription_message(message)

        @self.bot.message_handler(func=lambda message: message.text in ["📢 Наш канал", "📢 Bizning kanal"])
        def channel_button(message):
            lang = self.user_languages.get(message.from_user.id, 'ru')
            try:
                channel_text = {
                    'ru': "📣 Подпишитесь на наш канал с новостями: [Bloger\\.Agensy](https://t.me/blogerAgensy)",
                    'uz': "📣 Yangiliklardan xabardor bo'lish uchun kanalimizga obuna bo'ling: [Bloger\\.Agensy](https://t.me/blogerAgensy)"
                }
                self.bot.send_message(
                    message.chat.id,
                    channel_text[lang],
                    parse_mode='MarkdownV2',
                    disable_web_page_preview=True
                )
            except Exception as e:
                logger.error(f"Error in channel_button: {e}")
                self.send_error_message(message)

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