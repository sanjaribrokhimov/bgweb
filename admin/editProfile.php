<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пользователя</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        
    </style>
</head>
<body>
    <div class="container-fluid p-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center mb-0">Редактирование профиля пользователя</h4>
                    </div>
                    <div class="card-body">
                        <form id="editProfileForm">
                            <input type="hidden" id="userId" name="userId">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Имя</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input style="color: black;"  type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Телефон</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input style="color: black;" type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input style="color: black;" type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="telegram" class="form-label">Telegram</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-telegram"></i></span>
                                    <input style="color: black;" type="text" class="form-control" id="telegram" name="telegram" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="instagram" class="form-label">Instagram</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                    <input style="color: black;" type="text" class="form-control" id="instagram" name="instagram" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Категория</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Выберите категорию</option>
                                        <option value="blogger">Блогер</option>
                                        <option value="company">Компания</option>
                                        <option value="freelancer">Фрилансер</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3" id="directionContainer" style="display: none;">
                                <label for="direction" class="form-label">Направление</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-stream"></i></span>
                                    <select class="form-select" id="direction" name="direction" required>
                                        <!-- Опции будут добавлены динамически -->
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Сохранить
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">
                                    <i class="fas fa-times"></i> Отмена
                                </button>
                            </div>
                        </form>

                        <div id="alertMessage" class="alert mt-3" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Направления для каждой категории
        const directions = {
            blogger: {
                lifestyle: "Лайфстайл и влог",
                fashion: "Мода и стиль",
                beauty: "Бьюти и косметика",
                travel: "Путешествия и туризм",
                food: "Еда и кулинария",
                sport: "Спорт и фитнес",
                business: "Бизнес и предпринимательство",
                education: "Образование и саморазвитие",
                technology: "Технологии и гаджеты",
                gaming: "Игры и киберспорт",
                music: "Музыка и развлечения",
                art: "Искусство и творчество",
                health: "Здоровье и wellness",
                parenthood: "Родительство и семья",
                pets: "Домашние животные",
                cars: "Автомобили и транспорт",
                finance: "Финансы и инвестиции",
                motivation: "Мотивация и психология"
            },
            company: {
                retail: "Розничная торговля",
                wholesale: "Оптовая торговля",
                services: "Услуги и сервис",
                manufacturing: "Производство",
                tech: "IT и технологии",
                finance: "Финансы и банкинг",
                construction: "Строительство",
                realestate: "Недвижимость",
                healthcare: "Здравоохранение",
                education: "Образование",
                hospitality: "Гостиничный бизнес",
                restaurant: "Рестораны и общепит",
                logistics: "Логистика",
                agriculture: "Сельское хозяйство",
                energy: "Энергетика",
                media: "Медиа и развлечения",
                consulting: "Консалтинг",
                automotive: "Автомобильный бизнес",
                mall: "Торговые центры",

            },
            freelancer: {
                webdev: "Веб-разработка",
                mobiledev: "Мобильная разработка",
                uidesign: "UI/UX Дизайн",
                graphicdesign: "Графический дизайн",
                marketing: "Маркетинг",
                smm: "SMM",
                copywriting: "Копирайтинг",
                translation: "Перевод",
                video: "Видеопроизводство",
                animation: "Анимация",
                voiceover: "Озвучка",
                photography: "Фотография",
                "3d": "3D моделирование",
                gamedev: "Разработка игр",
                seo: "SEO-оптимизация",
                analytics: "Аналитика",
                consulting: "Консультирование",
                projectmanagement: "Управление проектами"
            }
        };

        // Функция для обновления списка направлений
        function updateDirections(category, selectedDirection = '') {
            const directionSelect = document.getElementById('direction');
            const directionContainer = document.getElementById('directionContainer');
            
            directionSelect.innerHTML = '<option value="">Выберите направление</option>';
            
            if (category && directions[category]) {
                Object.entries(directions[category]).forEach(([value, text]) => {
                    const option = new Option(text, value);
                    directionSelect.add(option);
                });
                
                if (selectedDirection) {
                    directionSelect.value = selectedDirection;
                }
                
                directionContainer.style.display = 'block';
            } else {
                directionContainer.style.display = 'none';
            }
        }

        // При загрузке страницы
        document.addEventListener('DOMContentLoaded', async () => {
            const userEmail = new URLSearchParams(window.location.search).get('email');
            if (!userEmail) {
                showAlert('Не указан email пользователя', 'danger');
                return;
            }

            document.getElementById('email').value = userEmail;

            try {
                // Получаем данные пользователя по email
                const response = await fetch(`https://blogy.uz/api/auth/user?email=${encodeURIComponent(userEmail)}`);
                const userData = await response.json();

                if (!response.ok) {
                    throw new Error(userData.message || 'Ошибка получения данных');
                }

                // Заполняем форму
                document.getElementById('name').value = userData.name || '';
                document.getElementById('phone').value = userData.phone || '';
                document.getElementById('email').value = userData.email || '';
                document.getElementById('telegram').value = userData.telegram || '';
                document.getElementById('instagram').value = userData.instagram || '';
                document.getElementById('category').value = userData.category || '';

                // Обновляем направления
                if (userData.category) {
                    updateDirections(userData.category, userData.direction);
                }

            } catch (error) {
                console.error('Ошибка:', error);
                showAlert('Ошибка загрузки данных пользователя: ' + error.message, 'danger');
            }
        });

        // Обработчик изменения категории
        document.getElementById('category').addEventListener('change', function() {
            updateDirections(this.value);
        });

        // Обработчик отправки формы
        document.getElementById('editProfileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            try {
                const formData = {
                    email: form.email.value,
                    name: form.name.value,
                    phone: form.phone.value,
                    telegram: form.telegram.value,
                    instagram: form.instagram.value,
                    category: form.category.value,
                    direction: document.getElementById('direction').value,
                };
                
                formData.tg_chat_id = "chat_id";
                formData.tg_user_id = "user_id";

                console.log('Отправляемые данные:', formData); // Для отладки

                const response = await fetch('https://blogy.uz/api/auth/update-profile', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                console.log('Ответ сервера:', result); // Для отладки

                if (!response.ok) {
                    throw new Error(result.message || 'Ошибка обновления данных');
                }

                showAlert('Профиль успешно обновлен', 'success');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);

            } catch (error) {
                console.error('Ошибка:', error);
                showAlert(error.message, 'danger');
            } finally {
                submitBtn.disabled = false;
            }
        });

        function showAlert(message, type) {
            const alertDiv = document.getElementById('alertMessage');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            alertDiv.style.display = 'block';
        }
    </script>
</body>
</html> 