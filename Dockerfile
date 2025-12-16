# Sử dụng môi trường PHP kèm Apache
FROM php:8.2-apache

# Cài đặt extension mysqli để kết nối database (nếu cần)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy toàn bộ code của bạn vào thư mục web của server
COPY . /var/www/html/

# Mở cổng 80 để truy cập
EXPOSE 80