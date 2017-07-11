# RUN AND DEPLOY 
# DỰ ÁN QUẢN LÝ VẬN TẢI - HOÀNG NGUYỄN

## 1. Required

- Server:
  + php >= 7x
  + composer >= 1x
  + mysql >= 5x
- Client:
  + nodejs >= 6x
  + npm >= 4x

- IDE:
  + PhpStorm (Recommended)
  + ...
-----------------------------------
## 2. Run & deploy

- config .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- create database
- ```composer install``` (Lệnh này cũng sẽ xóa tất cả các bảng và tạo lại, cẩn thận mất data)
- ```php artisan serve```
- cd public/dev: ```npm install```
- cd public/dev/src: ```typings install``` (optional)
- cd public/dev: ```npm run dev```
- Access: http://localhost:8000

### Note:
- Trong file .env có trường APP_PROD
  + true: không tạo dữ liệu test
  + false: tạo dữ liệu test

-----------------------------------
## Tip & Trick

- Create component:            ```ng g c components/component-name```
- Create component (plain):    ```ng g c components/component-name -is --spec false```
- Create service:              ```ng g s services/service-name-folder/service-name```
- Build to dev:                ```ng build --bh /home/ -op ../home -w```
- Buid to prod:                ```ng build --bh /home/ -op ../home -prod -e=prod```
- Buid to prod (full):         ```ng build --base-href /home/ --output-path ../home --target=production --environment=prod```
- Run outside Angular:         ```ng serve --host 0.0.0.0```
- Run outside Laravel:         ```php artisan serve --host=0.0.0.0 --port=8000```