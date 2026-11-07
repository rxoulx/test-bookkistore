#!/bin/bash

# ====================================================================
# SCRIPT KIỂM TRA ĐỐI CHIẾU 100% KIẾN THỨC TỪ 2 SƠ ĐỒ CÔNG NGHỆ
# ====================================================================

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}====================================================================${NC}"
echo -e "${BLUE}   KIỂM TRA HẠ TẦNG: DOCKER, CLOUD, RAID BODDB, LXC & LACP         ${NC}"
echo -e "${BLUE}====================================================================${NC}"
echo ""

# 1. KIỂM TRA DOCKER VÀ CÁC SERVICES (HÌNH 1 & HÌNH 2)
echo -e "${YELLOW}>> 1. Kiểm tra Docker Cluster & Services (Nginx, Web, DB, Telemetry):${NC}"
if docker compose ps 2>&1 | grep -q "Up"; then
    echo -e "   [${GREEN}OK${NC}] Docker Containers đang chạy:"
    docker compose ps --format "table {{.Service}}\t{{.State}}\t{{.Ports}}"
else
    echo -e "   [${RED}FAIL${NC}] Docker containers chưa chạy. Hãy chạy 'bash start.sh'!"
fi
echo ""

# 2. KIỂM TRA MẠNG & PHÂN VÙNG (DMZ vs PRIVATE vs BRIDGE)
echo -e "${YELLOW}>> 2. Kiểm tra Mạng & Phân vùng (DMZ, Bridge, Private):${NC}"
if docker network inspect bookkistore-main_frontend-net &>/dev/null || docker network ls | grep -q "frontend-net"; then
    echo -e "   [${GREEN}OK${NC}] Bridge Network 'frontend-net' (DMZ cho Nginx): Đã kích hoạt"
    echo -e "   [${GREEN}OK${NC}] Bridge Network 'backend-net' (Private cho DB BODDB): Đã kích hoạt"
else
    echo -e "   [${YELLOW}INFO${NC}] Kiểm tra lại tên network bằng: docker network ls"
fi

# Kiểm tra Nginx Proxy (Port 8080)
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 || echo "000")
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
    echo -e "   [${GREEN}OK${NC}] Portal / Nginx Proxy (Port 8080 DMZ): Phản hồi HTTP $HTTP_CODE"
else
    echo -e "   [${YELLOW}WARN${NC}] Portal / Nginx Proxy phản hồi: HTTP $HTTP_CODE"
fi
echo ""

# 3. KIỂM TRA LƯU TRỮ RAID 1 MIRROR (HÌNH 1)
echo -e "${YELLOW}>> 3. Kiểm tra Lưu trữ RAID 1 (Mirror - Sao chép dữ liệu 2 ổ):${NC}"
if [ -e /dev/md0 ] && grep -q "md0" /proc/mdstat 2>/dev/null; then
    RAID_STATUS=$(grep -A 1 "md0" /proc/mdstat | tail -n 1)
    echo -e "   [${GREEN}OK${NC}] Mảng RAID 1 (/dev/md0) đang hoạt động: $RAID_STATUS"
    if mountpoint -q /mnt/raid1-storage; then
        echo -e "   [${GREEN}OK${NC}] Phân vùng RAID 1 đã mount tại: /mnt/raid1-storage"
        df -h /mnt/raid1-storage | tail -n 1 | awk '{print "         Dung lượng: " $2 ", Đã dùng: " $3 ", Khả dụng: " $4}'
    fi
else
    echo -e "   [${RED}FAIL${NC}] Chưa phát hiện mảng RAID 1. Chạy 'sudo bash scripts/setup_system.sh' để tạo!"
fi
echo ""

# 4. KIỂM TRA MẠNG LACP / BONDING (HÌNH 2)
echo -e "${YELLOW}>> 4. Kiểm tra LACP / BOND / TEAMING (Gộp băng thông & Dự phòng):${NC}"
if [ -f /proc/net/bonding/bond0 ]; then
    BOND_MODE=$(grep "Bonding Mode:" /proc/net/bonding/bond0 | cut -d: -f2 | xargs)
    SLAVES=$(grep "Slave Interface:" /proc/net/bonding/bond0 | awk '{print $3}' | tr '\n' ' ')
    echo -e "   [${GREEN}OK${NC}] Interface bond0 đang chạy: Mode = $BOND_MODE"
    echo -e "   [${GREEN}OK${NC}] Các card mạng thành viên gộp lại: $SLAVES"
else
    echo -e "   [${RED}FAIL${NC}] Chưa thấy interface bond0. Chạy 'sudo bash scripts/setup_system.sh' để tạo!"
fi
echo ""

# 5. KIỂM TRA ẢO HÓA LXC (HÌNH 2)
echo -e "${YELLOW}>> 5. Kiểm tra Ảo hóa cấp HĐH - LXC (Linux Containers):${NC}"
if command -v lxc &>/dev/null && lxc list &>/dev/null; then
    LXC_COUNT=$(lxc list -c s --format csv | grep -c "RUNNING" || true)
    echo -e "   [${GREEN}OK${NC}] LXC Daemon (LXD) đang hoạt động. Số container đang RUNNING: $LXC_COUNT"
    lxc list -c ns4
else
    echo -e "   [${RED}FAIL${NC}] LXC chưa sẵn sàng hoặc user chưa thuộc nhóm lxd."
fi
echo ""

# 6. KIỂM TRA TELEMETRY TRANSPORT & GIÁM SÁT (HÌNH 1)
echo -e "${YELLOW}>> 6. Kiểm tra Telemetry Transport (cAdvisor Port 8088):${NC}"
CADVISOR_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8088 || echo "000")
if [ "$CADVISOR_CODE" = "200" ]; then
    echo -e "   [${GREEN}OK${NC}] Telemetry cAdvisor đang chạy tại http://localhost:8088 (HTTP 200)"
else
    echo -e "   [${YELLOW}WARN${NC}] Telemetry cAdvisor phản hồi: HTTP $CADVISOR_CODE"
fi
echo ""

# 7. KIỂM TRA CLOUD BACKUP (DROPBOX / RCLONE) (HÌNH 1 & HÌNH 2)
echo -e "${YELLOW}>> 7. Kiểm tra Sao lưu & Đồng bộ Cloud (Dropbox):${NC}"
if command -v rclone &>/dev/null; then
    if rclone listremotes 2>/dev/null | grep -q "^dropbox:"; then
        echo -e "   [${GREEN}OK${NC}] Rclone đã liên kết tài khoản Dropbox thành công!"
        echo "   Bản sao lưu trên Dropbox:"
        rclone lsf dropbox:Bookstore_Backup 2>/dev/null || echo "         (Chưa có file backup, hãy chạy bash scripts/backup_boddb.sh)"
    else
        echo -e "   [${YELLOW}INFO${NC}] Rclone đã cài đặt nhưng chưa liên kết Dropbox. Chạy 'bash scripts/setup_dropbox.sh' khi cần."
    fi
else
    echo -e "   [${RED}FAIL${NC}] Rclone chưa được cài đặt."
fi
echo ""

echo -e "${BLUE}====================================================================${NC}"
echo -e "${BLUE}   KẾT THÚC BÁO CÁO KIỂM TRA TOÀN DIỆN HỆ THỐNG                     ${NC}"
echo -e "${BLUE}====================================================================${NC}"
