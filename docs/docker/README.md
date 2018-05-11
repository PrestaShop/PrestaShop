This repository contains examples to run PrestaShop with docker & docker-compose

# Requirements
- docker 18+
- docker-compose 1.17+

# Instructions
* prestashopCli.sh: script to download PrestaShop sources and build a release
* nginx_fpm: docker-compose with 3 containers: mysql, nginx, prestashop-fpm

After installation the name of your admin dir must be 'admin'.
You can edit this value in the nginx config file (nginx_fpm/prestashop-nginx/prestashop-nginx.conf) 

To test:
```
./prestashopCli.sh
cd nginx_fpm
docker-compose up
```

You can launch the installation of [your shop here](http://localhost:8080)
