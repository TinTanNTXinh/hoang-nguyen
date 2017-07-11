# RUN AND DEPLOY 
# DỰ ÁN QUẢN LÝ VẬN TẢI - HOÀNG NGUYỄN

## 1. Required

- Server:
  + php >= 7x
  + composer >= 1x
  + mysql
- Client:
  + nodejs >= 6x
  + npm >= 4x

- IDE:
  + PhpStorm (Recommended)
  + ...
-----------------------------------
## 2. Run & deploy

- config .env
- create database
- composer install
- cd public/dev: npm install
- cd public/dev/src: typings install (optional)
- cd public/dev: npm run dev
- php artisan serve
- Access: http://localhost:8000

-----------------------------------
## Tip & Trick

- Create component:            ng g c components/component-name
- Create component (plain):    ng g c components/component-name -is --spec false
- Create service:              ng g s services/service-name-folder/service-name
- Build to dev:                ng build --bh /home/ -op ../home -w
- Buid to prod:                ng build --bh /home/ -op ../home -prod -e=prod
- Buid to prod (full):         ng build --base-href /home/ --output-path ../home --target=production --environment=prod
- Run outside Angular:         ng serve --host 0.0.0.0
- Run outside Laravel:         php artisan serve --host=0.0.0.0 --port=8000