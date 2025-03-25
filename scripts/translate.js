

function translateText(text, targetLang) {
    return fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=${targetLang}&dt=t&q=${encodeURI(text)}`)
        .then(response => response.json())
        .then(data => data[0][0][0])
        .catch(error => console.error('Ошибка перевода:', error));
}
const translationExceptions = {
    "Подробнее": { "ru": "Подробнее", "uz": "Batafsil" },
    "Batafsil": { "ru": "Подробнее", "uz": "Batafsil" },
    "Блогеры": { "ru": "Блогеры", "uz": "Blogerlar" },
    "Blogerlar": { "ru": "Блогеры", "uz": "Blogerlar" },
    "Компании": { "ru": "Компании", "uz": "Kompaniyalar" },
    "Kompaniyalar": { "ru": "Компании", "uz": "Kompaniyalar" },
    "Фрилансеры": { "ru": "Фрилансеры", "uz": "Frilanserlar" },
    "Frilanserlar": { "ru": "Фрилансеры", "uz": "Frilanserlar" },
    "Добавить": { "ru": "Добавить", "uz": "Qoshish" },
    "Qoshish": { "ru": "Добавить", "uz": "Qoshish" },
    "Сделка": { "ru": "Сделка", "uz": "Kelishuv" },
    "Kelishuv": { "ru": "Сделка", "uz": "Kelishuv" },
    "Мухаммаддиёр": { "ru": "Мухаммаддиёр", "uz": "Mukhammaddiyor" },
    "Mukhammaddiyor": { "ru": "Мухаммаддиёр", "uz": "Mukhammaddiyor" },
    "Мой профиль": { "ru": "Мой профиль", "uz": "Mening profilim" },
    "Mening profilim": { "ru": "Мой профиль", "uz": "Mening profilim" },
    "Мои объявления": { "ru": "Мои объявления", "uz": "Mening e\'lonlarim" },
    "Mening e\'lonlarim": { "ru": "Мои объявления", "uz": "Mening e\'lonlarim" },
    "Выйти": { "ru": "Выйти", "uz": "Chiqish" },
    "Chiqish": { "ru": "Выйти", "uz": "Chiqish" },
    "Изменить пароль": { "ru": "ВыйтИзменить парольи", "uz": "Parolni yangilash" },
    "Parolni yangilash": { "ru": "Изменить пароль", "uz": "Parolni yangilash" },
    "Войти": { "ru": "Войти", "uz": "Kirish" },
    "Kirish": { "ru": "Войти", "uz": "Kirish" },
    "Запомнить меня": { "ru": "Запомнить меня", "uz": "Eslab qolish" },
    "Eslab qolish": { "ru": "Запомнить меня", "uz": "Eslab qolish" },
    "Вход": { "ru": "Вход", "uz": "Kirish" },
    "Kirish": { "ru": "Вход", "uz": "Kirish" },
    "По телеграму": { "ru": "По телеграму", "uz": "Telegramdan" },
    "Telegramdan": { "ru": "По телеграму", "uz": "Telegramdan" },
    "Telegram": { "ru": "Telegram", "uz": "Telegram" },
    "Опубликовать": { "ru": "Опубликовать", "uz": "E'lon joylash" },
    "E'lon joylash": { "ru": "Опубликовать", "uz": "E'lon joylash" },
    "Сохранить": { "ru": "Сохранить", "uz": "Saqlash" },
    "Saqlash": { "ru": "Сохранить", "uz": "Saqlash" },
    "Комментарий к рекламе": {"ru": "Комментарий к рекламе", "uz": "Reklama sharhi"},
    "Reklama sharhi": {"ru": "Комментарий к рекламе", "uz": "Reklama sharhi"},
    "Новый пароль": { "ru": "Новый пароль", "uz": "Yangi parol" },
    "Yangi parol": { "ru": "Новый пароль", "uz": "Yangi parol" },
};

// function translatePage(targetLang) {
//     console.log('zapusk')
//     $('.translate').each(function () {
//         let element = $(this);
//         let originalText = element.text().trim();
//         let wordsCount = originalText.split(" ").length;

//         // Проверяем, находится ли элемент внутри карточки
//         let isInCard = element.closest('.product-card .tr').length > 0;

//         // Если элемент внутри карточки и текст состоит из одного слова, не переводим его
//         if ((isInCard && wordsCount === 1)) {
//             return;
//         }
//         if(originalText.length > 200){
//             console.log(originalText);
//         }
//         // Проверяем, есть ли слово/фраза в исключениях
//         console.log(originalText, targetLang);
//         if (translationExceptions[originalText] && translationExceptions[originalText][targetLang]) {
//             element.text(translationExceptions[originalText][targetLang]);
//         } else {
//             // Если нет, переводим через API
//             translateText(originalText, targetLang).then(translatedText => {
//                 element.text(translatedText);
//             });
//         }
//     });
// }

function translatePage(targetLang) {
    console.log('Translation started');
    
    // Translate text elements
    $('.translate').each(function () {
        let element = $(this);
        let originalText = element.text().trim();
        let wordsCount = originalText.split(" ").length;
        
        // Check if element is inside a product card
        let isInCard = element.closest('.product-card .tr').length > 0;
        
        // Skip translation for single-word elements in cards
        if ((isInCard && wordsCount === 1)) {
            return;
        }
        
        if(originalText.length > 200){
            console.log(originalText);
        }
        
        // Check for translation exceptions
        if (translationExceptions[originalText] && translationExceptions[originalText][targetLang]) {
            element.text(translationExceptions[originalText][targetLang]);
        } else {
            // Translate via API if no exception
            translateText(originalText, targetLang).then(translatedText => {
                element.text(translatedText);
            });
        }
    });
    
    // Translate input and textarea placeholders
    $('input[placeholder], textarea[placeholder]').each(function() {
        let element = $(this);
        let originalPlaceholder = element.attr('placeholder').trim();
        
        // Check for placeholder translation exceptions
        if (translationExceptions[originalPlaceholder] && translationExceptions[originalPlaceholder][targetLang]) {
            element.attr('placeholder', translationExceptions[originalPlaceholder][targetLang]);
        } else {
            // Translate placeholder via API if no exception
            translateText(originalPlaceholder, targetLang).then(translatedPlaceholder => {
                element.attr('placeholder', translatedPlaceholder);
            });
        }
    });
}