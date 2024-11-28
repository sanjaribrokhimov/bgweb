#!/bin/bash

# Обновление системы
sudo apt-get update
sudo apt-get upgrade -y

# Установка PostgreSQL
sudo apt-get install postgresql postgresql-contrib -y

# Установка Go (если еще не установлен)
wget https://go.dev/dl/go1.22.0.linux-amd64.tar.gz
sudo rm -rf /usr/local/go && sudo tar -C /usr/local -xzf go1.22.0.linux-amd64.tar.gz
export PATH=$PATH:/usr/local/go/bin

# Создание .env файла
cat > .env << EOL
DB_HOST=localhost
DB_PORT=5432
DB_USER=your_db_user
DB_PASSWORD=your_db_password
DB_NAME=your_db_name

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_EMAIL=your_email@gmail.com
SMTP_PASSWORD=your_email_password
EOL

# Настройка PostgreSQL
sudo -u postgres psql << EOF
CREATE DATABASE your_db_name;
CREATE USER your_db_user WITH PASSWORD 'your_db_password';
GRANT ALL PRIVILEGES ON DATABASE your_db_name TO your_db_user;
\q
EOF

# Установка зависимостей Go
go mod download

# Сборка проекта
go build -o app

# Создание systemd сервиса
sudo tee /etc/systemd/system/bloger-agency.service << EOL
[Unit]
Description=Bloger Agency Backend
After=network.target

[Service]
Type=simple
User=$USER
WorkingDirectory=$(pwd)
ExecStart=$(pwd)/app
Restart=always

[Install]
WantedBy=multi-user.target
EOL

# Запуск сервиса
sudo systemctl daemon-reload
sudo systemctl enable bloger-agency
sudo systemctl start bloger-agency

echo "Установка завершена!" 