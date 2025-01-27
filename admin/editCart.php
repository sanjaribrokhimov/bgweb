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
    <title>Редактирование объявления</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Редактирование объявления</h2>
        <div id="editForm" class="card p-4">
            <form id="adEditForm">
                <input type="hidden" id="adId">
                <input type="hidden" id="adType">
                
                <div class="mb-3">
                    <label for="name" class="form-label">Имя/Название</label>
                    <input type="text" class="form-control" id="name" required>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Категория</label>
                    <select class="form-control" id="category" required>
                        <option value="">Выберите категорию</option>
                        <option value="IT">IT</option>
                        <option value="Маркетинг">Маркетинг</option>
                        <option value="Дизайн">Дизайн</option>
                        <!-- Добавьте другие категории по необходимости -->
                    </select>
                </div>

                <div class="mb-3" id="directionBlock">
                    <label for="direction" class="form-label">Направление</label>
                    <input type="text" class="form-control" id="direction">
                </div>

                <div class="mb-3">
                    <label for="adComment" class="form-label">Описание</label>
                    <textarea class="form-control" id="adComment" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="photo" class="form-label">Фото</label>
                    <input type="file" class="form-control" id="photo" accept="image/*">
                    <div id="currentPhoto" class="mt-2"></div>
                </div>

                <div id="linksSection">
                    <div class="mb-3">
                        <label for="instagramLink" class="form-label">Instagram</label>
                        <input type="url" class="form-control" id="instagramLink">
                    </div>

                    <div class="mb-3">
                        <label for="telegramLink" class="form-label">Telegram</label>
                        <input type="url" class="form-control" id="telegramLink">
                    </div>

                    <!-- Дополнительные поля для разных типов -->
                    <div class="mb-3 blogger-field">
                        <label for="youtubeLink" class="form-label">YouTube</label>
                        <input type="url" class="form-control" id="youtubeLink">
                    </div>

                    <div class="mb-3 company-field">
                        <label for="websiteLink" class="form-label">Веб-сайт</label>
                        <input type="url" class="form-control" id="websiteLink">
                    </div>

                    <div class="mb-3 company-field">
                        <label for="budget" class="form-label">Бюджет</label>
                        <input type="number" class="form-control" id="budget">
                    </div>

                    <div class="mb-3 freelancer-field">
                        <label for="githubLink" class="form-label">GitHub</label>
                        <input type="url" class="form-control" id="githubLink">
                    </div>

                    <div class="mb-3 freelancer-field">
                        <label for="portfolioLink" class="form-label">Портфолио</label>
                        <input type="url" class="form-control" id="portfolioLink">
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Отмена</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Получаем параметры из URL
        const urlParams = new URLSearchParams(window.location.search);
        const adId = urlParams.get('id');
        const adType = urlParams.get('type');

        // Устанавливаем значения в скрытые поля
        document.getElementById('adId').value = adId;
        document.getElementById('adType').value = adType;

        // Показываем/скрываем поля в зависимости от типа объявления
        function showRelevantFields() {
            document.querySelectorAll('.blogger-field, .company-field, .freelancer-field').forEach(el => {
                el.style.display = 'none';
            });

            const fieldsToShow = document.querySelectorAll(`.${adType}-field`);
            fieldsToShow.forEach(el => {
                el.style.display = 'block';
            });

            // Показываем/скрываем поле направления
            document.getElementById('directionBlock').style.display = 
                (adType === 'blogger' || adType === 'company') ? 'block' : 'none';
        }

        // Загружаем данные объявления
        async function loadAdData() {
            try {
                const response = await fetch(`https://blogy.uz/api/admin/get-ad?id=${adId}&type=${adType}`);
                const data = await response.json();

                document.getElementById('name').value = data.name;
                document.getElementById('category').value = data.category;
                document.getElementById('direction').value = data.direction || '';
                document.getElementById('adComment').value = data.ad_comment;
                
                // Заполняем ссылки
                document.getElementById('instagramLink').value = data.instagram_link || '';
                document.getElementById('telegramLink').value = data.telegram_link || '';
                
                if (adType === 'blogger') {
                    document.getElementById('youtubeLink').value = data.youtube_link || '';
                } else if (adType === 'company') {
                    document.getElementById('websiteLink').value = data.website_link || '';
                    document.getElementById('budget').value = data.budget || '';
                } else if (adType === 'freelancer') {
                    document.getElementById('githubLink').value = data.github_link || '';
                    document.getElementById('portfolioLink').value = data.portfolio_link || '';
                }

                // Показываем текущее фото
                if (data.photo) {
                    const imgElement = document.createElement('img');
                    imgElement.src = data.photo;
                    imgElement.style.maxWidth = '200px';
                    imgElement.style.maxHeight = '200px';
                    document.getElementById('currentPhoto').innerHTML = '';
                    document.getElementById('currentPhoto').appendChild(imgElement);
                }
            } catch (error) {
                console.error('Ошибка при загрузке данных:', error);
                alert('Ошибка при загрузке данных объявления');
            }
        }

        // Обработка отправки формы
        document.getElementById('adEditForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData();
            formData.append('id', adId);
            formData.append('type', adType);
            formData.append('name', document.getElementById('name').value);
            formData.append('category', document.getElementById('category').value);
            formData.append('direction', document.getElementById('direction').value);
            formData.append('ad_comment', document.getElementById('adComment').value);

            const photoInput = document.getElementById('photo');
            if (photoInput.files[0]) {
                formData.append('photo', photoInput.files[0]);
            }

            // Добавляем ссылки
            const links = {
                instagram_link: document.getElementById('instagramLink').value,
                telegram_link: document.getElementById('telegramLink').value
            };

            if (adType === 'blogger') {
                links.youtube_link = document.getElementById('youtubeLink').value;
            } else if (adType === 'company') {
                links.website_link = document.getElementById('websiteLink').value;
                links.budget = document.getElementById('budget').value;
            } else if (adType === 'freelancer') {
                links.github_link = document.getElementById('githubLink').value;
                links.portfolio_link = document.getElementById('portfolioLink').value;
            }

            formData.append('links', JSON.stringify(links));

            try {
                const response = await fetch('https://blogy.uz/api/admin/edit-ad', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    alert('Объявление успешно обновлено');
                    window.location.href = 'index.php';
                } else {
                    alert('Ошибка при обновлении объявления: ' + result.error);
                }
            } catch (error) {
                console.error('Ошибка при отправке данных:', error);
                alert('Ошибка при обновлении объявления');
            }
        });

        // Инициализация
        showRelevantFields();
        loadAdData();
    </script>
</body>
</html>
