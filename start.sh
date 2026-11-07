#!/bin/bash
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

echo "=== [1/3] Khởi tạo cấu hình môi trường (.env) ==="
if [ ! -f .env ]; then
    cp .env.example .env
    KEY="$(openssl rand -base64 32)"
    sed -i "s|^APP_KEY=.*|APP_KEY=base64:$KEY|" .env
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=secret123|" .env
    echo ">> Đã tạo file .env mới với APP_KEY và DB_PASSWORD"
else
    echo ">> File .env đã tồn tại."
fi

echo "=== [2/3] Khởi chạy các Docker Containers (Proxy Nginx, Web, Postgres DB, Telemetry) ==="
if command -v docker &> /dev/null && docker compose version &> /dev/null; then
    DOCKER_CMD="docker compose"
else
    DOCKER_CMD="sudo docker compose"
fi

$DOCKER_CMD up --build -d

echo "=== [3/3] Chạy Database Migrations ==="
$DOCKER_CMD exec web php artisan migrate --force

echo ""
echo "======================================================================"
echo "  HỆ THỐNG BOOKSTORE ĐÃ KHỞI CHẠY THÀNH CÔNG!"
echo "======================================================================"
echo " - Web Bookstore (Portal / Nginx DMZ) : http://localhost:8080"
echo " - Telemetry Transport (cAdvisor)     : http://localhost:8088"
echo " - Phân vùng an toàn RAID 1           : /mnt/raid1-storage"
echo " - LACP / Bonding Interface           : bond0 (mode 802.3ad)"
echo " - LXC Container                      : lxc-client"
echo "======================================================================"
echo " >> Để sao lưu DB lên RAID 1 và Dropbox: bash scripts/backup_boddb.sh"
echo "======================================================================"
