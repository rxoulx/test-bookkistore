#!/bin/bash
set -e

echo "=========================================================="
echo " [1/5] CÀI ĐẶT CÁC GÓI HỆ THỐNG VÀ CÔNG CỤ NỀN TẢNG"
echo "=========================================================="
sudo apt update
sudo apt install -y mdadm ifenslave bridge-utils htop glances curl git unzip rclone

# Cài Docker nếu chưa có
if ! command -v docker &> /dev/null; then
    echo ">> Đang cài Docker Engine..."
    curl -fsSL https://get.docker.com | sudo sh
    sudo usermod -aG docker "$USER"
fi

# Cài LXD (Ảo hóa cấp HĐH - LXC) nếu chưa có
if ! command -v lxc &> /dev/null; then
    echo ">> Đang cài LXD..."
    sudo snap install lxd
    sudo lxd init --auto
fi

echo "=========================================================="
echo " [2/5] THIẾT LẬP LƯU TRỮ RAID 1 MIRROR (/mnt/raid1-storage)"
echo "=========================================================="
# Kiểm tra nếu /dev/md0 chưa tồn tại thì tạo
if ! sudo mdadm --detail /dev/md0 &> /dev/null; then
    sudo mkdir -p /var/raid-disks
    sudo fallocate -l 1G /var/raid-disks/disk1.img
    sudo fallocate -l 1G /var/raid-disks/disk2.img

    sudo losetup -f /var/raid-disks/disk1.img
    LOOP1=$(sudo losetup -j /var/raid-disks/disk1.img | cut -d: -f1 | head -n1)
    
    sudo losetup -f /var/raid-disks/disk2.img
    LOOP2=$(sudo losetup -j /var/raid-disks/disk2.img | cut -d: -f1 | head -n1)

    echo ">> Tạo mảng RAID 1 với các thiết bị: $LOOP1 và $LOOP2"
    sudo mdadm --create /dev/md0 --level=1 --raid-devices=2 "$LOOP1" "$LOOP2" --metadata=0.90 --run
    sudo mkfs.ext4 -F /dev/md0
fi

sudo mkdir -p /mnt/raid1-storage
if ! mountpoint -q /mnt/raid1-storage; then
    sudo mount /dev/md0 /mnt/raid1-storage
fi
sudo chmod 777 /mnt/raid1-storage
echo ">> Phân vùng RAID 1 đã sẵn sàng tại /mnt/raid1-storage"

echo "=========================================================="
echo " [3/5] CẤU HÌNH MẠNG LACP / BONDING (bond0)"
echo "=========================================================="
sudo modprobe bonding
sudo modprobe dummy

if ! ip link show dummy0 &> /dev/null; then
    sudo ip link add dummy0 type dummy
fi
if ! ip link show dummy1 &> /dev/null; then
    sudo ip link add dummy1 type dummy
fi

if ! ip link show bond0 &> /dev/null; then
    sudo ip link add bond0 type bond mode 802.3ad
    sudo ip link set dummy0 master bond0
    sudo ip link set dummy1 master bond0
    sudo ip link set dummy0 up
    sudo ip link set dummy1 up
    sudo ip link set bond0 up
fi
echo ">> Interface bond0 (LACP 802.3ad) đã hoạt động"

echo "=========================================================="
echo " [4/5] KHỞI TẠO LXC CONTAINER (Linux Containers)"
echo "=========================================================="
if ! sudo lxc info lxc-client &> /dev/null; then
    echo ">> Đang khởi chạy container lxc-client..."
    sudo lxc launch ubuntu:22.04 lxc-client || true
fi

echo "=========================================================="
echo " [5/5] HOÀN TẤT THIẾT LẬP HẠ TẦNG"
echo "=========================================================="
echo "Hệ thống Ubuntu đã cài đặt đầy đủ:"
echo " - Docker & Docker Compose"
echo " - RAID 1 Mirror tại /mnt/raid1-storage"
echo " - LACP Bonding tại bond0"
echo " - LXC Container: lxc-client"
echo " - Rclone (Dropbox sync)"
echo ""
echo ">> Tiếp theo, bạn chỉ cần chạy: bash start.sh"
