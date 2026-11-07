# Dockerized Bookstore System (Enterprise Architecture)

Hệ thống ứng dụng Laravel quản lý sách (Bookstore) tích hợp kiến trúc hạ tầng toàn diện: **Docker, Cloud, RAID 1 (Mirror), LACP Bonding, LXC Containers và Cloud Backup (Dropbox)**.

---

## 🏗️ Kiến Trúc Hệ Thống (Ánh xạ 2 Sơ Đồ Công Nghệ)

```text
[ Client / Browser ] 
       │ (Port 8080)
       ▼
 [ Nginx Proxy / DMZ ] (frontend-net)
       │ (Port 9000 FastCGI)
       ▼
 [ Web App - Laravel ] (frontend-net + backend-net)
       │ (Port 5432)
       ▼
 [ DB - PostgreSQL (BODDB) ] (backend-net)
       │
       ├──► Local Storage: Named Volume `pgdata`
       │
       ├──► RAID 1 Storage (/mnt/raid1-storage): Sao lưu định kỳ (Mirror 2 ổ đĩa ảo)
       │
       └──► Cloud Storage (Dropbox): Đồng bộ bản sao lưu lên Internet / Cloud
```

### Các thành phần chính:
1. **Lưu trữ RAID 1 (Mirror)**: Mảng 2 ổ đĩa ảo qua `mdadm` mount tại `/mnt/raid1-storage`, đảm bảo an toàn dữ liệu chịu lỗi 1 ổ đĩa.
2. **Docker & Phân vùng mạng (Bridge / DMZ / Private)**:
   - `frontend-net`: Nginx (Proxy đối ngoại đóng vai trò DMZ/Portal) và Web.
   - `backend-net`: Mạng riêng nội bộ (Private) kết nối Web và Database PostgreSQL.
3. **Ảo hóa & Giám sát (Telemetry Transport)**:
   - Chạy trên máy ảo Ubuntu (VMware ESXi / Workstation).
   - Container **cAdvisor** (Port 8088) giám sát thời gian thực CPU, RAM, Telemetry mạng.
4. **Kiến trúc Mạng LACP & LXC**:
   - Giao tiếp mạng **LACP / Teaming (`bond0`)** gộp 2 interface tăng băng thông và dự phòng.
   - Hệ thống ảo hóa cấp HĐH **LXC Container** (`lxc-client`) chạy song song trên host.
5. **Đồng bộ Cloud (Dropbox)**:
   - Sử dụng `rclone` đồng bộ tự động dữ liệu từ RAID 1 lên Dropbox theo tiêu chuẩn sao lưu 3-2-1.

---

## 🚀 Hướng Dẫn Chạy Nhanh Trên Ubuntu VM

Sau khi clone dự án về máy ảo Ubuntu:

```bash
git clone <URL_REPO_CUA_BAN>
cd bookkistore-main
```

### 1. Cài đặt toàn bộ hạ tầng (Chạy 1 lần duy nhất)
Tự động cài đặt Docker, RAID 1, LACP, LXC, Rclone và các gói cần thiết:

```bash
sudo bash scripts/setup_system.sh
```

### 2. Khởi chạy toàn bộ hệ thống Bookstore
Tự động tạo file `.env`, sinh `APP_KEY`, khởi chạy 4 containers và chạy migration:

```bash
bash start.sh
```

- **Website Bookstore**: `http://localhost:8080` (hoặc `http://<IP_UBUNTU>:8080`)
- **Telemetry Monitor (cAdvisor)**: `http://localhost:8088`

### 3. Kiểm tra tự động toàn bộ hệ thống (Đối chiếu 100% 2 Sơ đồ)
Chạy script kiểm tra tổng hợp:
```bash
bash scripts/verify_all.sh
```
Script sẽ in ra bảng trạng thái chi tiết của Docker, Nginx DMZ, RAID 1, LACP bond0, LXC, cAdvisor và Dropbox.

### 4. (Tùy chọn) Thử nghiệm thêm RAID 5
Nếu muốn demo thêm nhánh **RAID 5** (chịu lỗi 1 ổ đĩa phân tán với 3 ổ):
```bash
sudo bash scripts/setup_raid5.sh
```

---

## 💾 Sao Lưu & Phục Hồi Dữ Liệu (BODDB & Dropbox)

### Cấu hình kết nối Dropbox (Tùy chọn)
Chạy script liên kết tài khoản Dropbox:
```bash
bash scripts/setup_dropbox.sh
```

### Sao lưu Database (BODDB ➔ RAID 1 ➔ Dropbox)
Chạy lệnh sau để xuất dump database ra phân vùng RAID 1 và đồng bộ lên Dropbox:
```bash
bash scripts/backup_boddb.sh
```

### Khôi phục Database từ bản sao lưu
```bash
# Khôi phục từ bản backup mới nhất trong RAID 1
bash scripts/restore_boddb.sh

# Hoặc chỉ định cụ thể file backup
bash scripts/restore_boddb.sh /mnt/raid1-storage/boddb_backup_20261107_120000.sql
```

---

## 🔍 Bảng Lệnh Kiểm Tra Hệ Thống (Verification)

| Thành phần | Lệnh kiểm tra |
|---|---|
| **Các Docker Containers** | `sudo docker compose ps` |
| **Mảng RAID 1 Mirror** | `cat /proc/mdstat` hoặc `sudo mdadm --detail /dev/md0` |
| **Giao diện LACP (bond0)** | `cat /proc/net/bonding/bond0` |
| **Container LXC** | `sudo lxc list` |
| **Thư mục backup RAID 1** | `ls -lh /mnt/raid1-storage/` |
| **Kiểm tra file trên Dropbox** | `rclone ls dropbox:Bookstore_Backup` |
| **Giám sát Telemetry** | Truy cập `http://localhost:8088` |
