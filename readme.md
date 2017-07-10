# HOANG NGUYEN PROJECT (Laravel 5.4 & Angular 4)

# [Mô tả logic dự án](https://github.com/TinTanNTXinh/hoang-nguyen/blob/master/MoTaLogicHoangNguyen.md) 

# [Mô tả cách tổ chức code dự án](https://github.com/TinTanNTXinh/hoang-nguyen/blob/master/MoTaCodeHoangNguyen.md)

# RUN & DEPLOY PROJECT
============================
## Required

- php >= 7x
- composer >= 1x
- nodejs >= 6x
- npm >= 4x
- mysql

-----------------------------------
## Official Documentation

- config .env
- create database
- composer install
- cd public/dev: npm install
- cd public/dev/src: typings install (optional)
- cd public/dev: npm run dev
- php artisan serve
- Access: http://localhost:8000
- Build prod: npm run prod (optional)
- Build prod: npm run go (optional) (to run outside on 0.0.0.0:4200)

-----------------------------------
## Tutorial Angular

- Create component:            ng g c components/component-name
- Create component (plain):    ng g c components/component-name -is --spec false
- Create service:              ng g s services/service-name-folder/service-name
- Build to dev:                ng build --bh /home/ -op ../home -w
- Buid to prod:                ng build --bh /home/ -op ../home -prod -e=prod
- Buid to prod (full):         ng build --base-href /home/ --output-path ../home --target=production --environment=prod
- Run outside Angular:         ng serve --host 0.0.0.0
- Run outside Laravel:         php artisan serve --host=0.0.0.0 --port=8000

Contact with me via [Facebook](http://facebook.com/nguyentrucxjnh), [Skype](ntxinh.tintansoft), [Gmail](ntxinh@tintansoft.com).
-----------------------------------
## Developer


-----------------------------------
## License

License belong to TinTanSoft Company.
