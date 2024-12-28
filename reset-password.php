<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- ... head content ... -->
</head>
<body>
    <div class="container-fluid p-3 app-container">
        <div class="auth-container">
            <h4 class="text-center mb-4">Изменение пароля</h4>
            <form id="resetPasswordForm">
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" placeholder="Новый пароль" required>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" placeholder="Подтвердите пароль" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Сохранить</button>
                
                <div id="resetPasswordResponse" class="mt-3" style="display: none;">
                    <div class="alert" role="alert"></div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = localStorage.getItem('resetPasswordEmail');
            const password = e.target.querySelector('input[type="password"]').value;
            
            try {
                const response = await fetch('https://blogy.uz/api/auth/reset-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();
                
                if (response.status === 200) {
                    // Очищаем данные восстановления
                    localStorage.removeItem('resetPasswordEmail');
                    localStorage.removeItem('resetPassword');
                    
                    // Перенаправляем на страницу входа
                    window.location.href = 'login.php';
                } else {
                    const responseBlock = document.getElementById('resetPasswordResponse');
                    const alertBlock = responseBlock.querySelector('.alert');
                    responseBlock.style.display = 'block';
                    alertBlock.className = 'alert alert-danger';
                    alertBlock.textContent = data.error;
                }
            } catch (error) {
                console.error('Ошибка:', error);
            }
        });
    </script>
</body>
</html> 