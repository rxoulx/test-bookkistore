#!/bin/bash
set -e

echo "=========================================================="
echo " THIẾT LẬP MẢNG LƯU TRỮ RAID 5 (PHÂN TÁN - CHỊU LỖI 1 Ổ)"
echo " (Theo nhánh 'RAID 5' trong sơ đồ Hệ Thống Công Nghệ)"
echo "=========================================================="

sudo mkdir -p /var/raid5-disks
sudo fallocate -l 1G /var/raid5-disks/disk1.img
sudo fallocate -l 1G /var/raid5-disks/disk2.img
sudo fallocate -l 1G /var/raid5-disks/disk3.img

sudo losetup -f /var/raid5-disks/disk1.img
LOOP1=$(sudo losetup -j /var/raid5-disks/disk1.img | cut -d: -f1 | head -n1)

sudo losetup -f /var/raid5-disks/disk2.img
LOOP2=$(sudo losetup -j /var/raid5-disks/disk2.img | cut -d: -f1 | head -n1)

sudo losetup -f /var/raid5-disks/disk3.img
LOOP3=$(sudo losetup -j /var/raid5-disks/disk3.img | cut -d: -f1 | head -n1)

echo ">> Tạo mảng RAID 5 (/dev/md1) với 3 ổ đĩa ảo: $LOOP1, $LOOP2, $LOOP3"
sudo mdadm --create /dev/md1 --level=5 --raid-devices=3 "$LOOP1" "$LOOP2" "$LOOP3" --metadata=0.90 --run

sudo mkfs.ext4 -F /dev/md1
sudo mkdir -p /mnt/raid5-storage
sudo mount /dev/md1 /mnt/raid5-storage
sudo chmod 777 /mnt/raid5-storage

echo ">> Hoàn tất! Phân vùng RAID 5 đã mount tại: /mnt/raid5-storage"
cat /proc/mdstat
