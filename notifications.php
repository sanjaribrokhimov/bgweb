<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Уведомления</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1.1.5">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .notifications-list {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .notification-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .notification-card.unread {
            background: var(--accent-blue-light);
            border-left: 4px solid var(--accent-blue);
        }

        .notification-card.unread .notification-body {
            font-weight: 500;
        }

        .notification-card.unread:hover {
            background: var(--accent-blue-hover);
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .notification-body {
            color: var(--text-color);
        }

        .user-info {
            color: var(--text-color);
        }

        .time {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .modal-content {
            background-color:black;
            border: 1px solid var(--border-color);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .modal-user-info,
        .modal-ad-info {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            background: var(--card-bg);
        }

        .modal-user-info h6,
        .modal-ad-info h6 {
            margin-bottom: 15px;
            color: var(--text-color);
        }

        .user-details p,
        .ad-details p {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-color);
        }

        .user-details i,
        .ad-details i {
            width: 20px;
            color: var(--text-secondary);
        }

        .user-details a {
            color: #0088cc;
            text-decoration: none;
        }

        .user-details a:hover {
            text-decoration: underline;
        }

        .empty-state,
        .error-state {
            text-align: center;
            padding: 40px;
            color: var(--text-secondary);
        }

        [data-bs-theme="dark"] .modal-content,
        [data-bs-theme="dark"] .notification-card {
            background: var(--dark-bg);
            border-color: var(--dark-border);
        }

        [data-bs-theme="dark"] .modal-header {
            border-color: var(--dark-border);
        }

        [data-bs-theme="dark"] .user-details p,
        [data-bs-theme="dark"] .ad-details p {
            color: var(--dark-text);
        }

        .notification-ad-info {
            margin: 8px 0;
            padding: 8px;
            background: var(--card-hover);
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .notification-ad-info p {
            margin: 0;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notification-ad-info i {
            color: var(--text-secondary);
        }

        .ad-link {
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .ad-link .btn {
            background: var(--accent-blue);
            border: none;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .ad-link .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .ad-link i {
            margin-right: 8px;
        }

        /* Стили для модального окна с объявлением */
        .modal-sections {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .ad-section, .sender-section {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 15px;
        }

        .ad-header, .sender-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .ad-type {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .ad-type.blogger { background: #e3f2fd; color: #1976d2; }
        .ad-type.company { background: #fce4ec; color: #c2185b; }
        .ad-type.freelancer { background: #e8f5e9; color: #388e3c; }

        .ad-content {
            display: flex;
            gap: 15px;
        }

        .ad-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
        }

        .ad-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ad-info {
            flex: 1;
        }

        .ad-info h5 {
            margin: 0 0 10px;
            color: var(--text-color);
        }

        .ad-comment {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .ad-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sender-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-color);
            margin-bottom: 8px;
        }

        .contact-item i {
            width: 20px;
            color: var(--text-secondary);
            text-align: center;
        }

        .date {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .telegram-link {
            color: #0088cc;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .telegram-link:hover {
            color: #0099ff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'components/miniHeader.php'; ?>

    <div class="container">
        <div class="notifications-list">
            <!-- Уведомления будут добавлены через JavaScript -->
        </div>
    </div>

    <!-- Модальное окно для деталей -->
    <div class="modal fade" id="notificationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-contentt">
                <div class="modal-header">
                    <h5 class="modal-title translate">Детали уведомления</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Содержимое будет добавлено динамически -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/notifications.js?v=1.0.1"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.notificationManager = new NotificationManager();
        });
    </script>
</body>
</html> 