#!/bin/bash
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT_DIR"

BACKUP_DIR="/mnt/raid1-storage"

if [ -z "$1" ]; then
    LATEST_BACKUP=$(ls -t "$BACKUP_DIR"/boddb_backup_*.sql 2>/dev/null | head -n 1)
    if [ -z "$LATEST_BACKUP" ]; then
        echo "[!] Không tìm thấy file backup nào trong $BACKUP_DIR."
        echo "Cách dùng: bash scripts/restore_boddb.sh [duong_dan_file_sql]"
        exit 1
    fi
    FILE_TO_RESTORE="$LATEST_BACKUP"
else
    FILE_TO_RESTORE="$1"
fi

echo ">> Đang khôi phục cơ sở dữ liệu từ: $FILE_TO_RESTORE"

if command -v docker &> /dev/null && docker compose version &> /dev/null; then
    DOCKER_CMD="docker compose"
else
    DOCKER_CMD="sudo docker compose"
fi

$DOCKER_CMD exec -T db psql -U bookstore_user -d bookstore < "$FILE_TO_RESTORE"
echo ">> [Thành công] Đã khôi phục dữ liệu BODDB vào PostgreSQL!"
