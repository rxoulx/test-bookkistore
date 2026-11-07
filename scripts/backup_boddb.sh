#!/bin/bash
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT_DIR"

BACKUP_DIR="/mnt/raid1-storage"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
FILENAME="boddb_backup_${TIMESTAMP}.sql"

if [ ! -d "$BACKUP_DIR" ]; then
    echo "[!] Phân vùng RAID 1 $BACKUP_DIR chưa tồn tại. Đang tạo tạm thư mục..."
    sudo mkdir -p "$BACKUP_DIR"
    sudo chmod 777 "$BACKUP_DIR"
fi

echo "=== 1. Xuất dữ liệu PostgreSQL (BODDB) từ Docker ==="
if command -v docker &> /dev/null && docker compose version &> /dev/null; then
    DOCKER_CMD="docker compose"
else
    DOCKER_CMD="sudo docker compose"
fi

$DOCKER_CMD exec -T db pg_dump -U bookstore_user bookstore > "${BACKUP_DIR}/${FILENAME}"
echo ">> [Thành công] Đã lưu bản sao lưu vào RAID 1: ${BACKUP_DIR}/${FILENAME}"

echo "=== 2. Kiểm tra và đồng bộ lên Cloud Dropbox ==="
if rclone listremotes | grep -q "^dropbox:"; then
    echo ">> Đang đẩy file backup lên Dropbox (thư mục: Bookstore_Backup)..."
    rclone copy "${BACKUP_DIR}/${FILENAME}" dropbox:Bookstore_Backup -P
    echo ">> [Thành công] Dữ liệu đã được bảo vệ trên Cloud Dropbox!"
else
    echo ">> [Lưu ý] Chưa cấu hình remote Dropbox. Hãy chạy 'bash scripts/setup_dropbox.sh' để liên kết Dropbox."
fi

echo ""
echo "=== Danh sách các bản backup hiện tại trong RAID 1 ==="
ls -lh "$BACKUP_DIR"
