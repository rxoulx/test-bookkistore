#!/bin/bash
echo "=========================================================="
echo " CẤU HÌNH KẾT NỐI DROPBOX QUA RCLONE"
echo "=========================================================="
echo "Trình hướng dẫn sẽ mở ra. Các bước thực hiện:"
echo " 1. Nhập 'n' (New remote)"
echo " 2. Đặt tên: 'dropbox'"
echo " 3. Chọn số tương ứng với dịch vụ: 'dropbox'"
echo " 4. Để trống Client ID và Client Secret (nhấn Enter)"
echo " 5. Xác thực tài khoản qua trình duyệt"
echo " 6. Xác nhận lưu cấu hình (y)"
echo "=========================================================="
rclone config
