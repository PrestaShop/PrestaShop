This repository contains examples to run PrestaShop with docker & docker-compose

# Requirements
- docker 18+
- docker-compose 1.17+
- composer 1.6+
- git

# Instructions
* prestashopCli.sh: script to download PrestaShop sources and build a release
* nginx_fpm: docker-compose with one container for each process: nginx, php-fpm, mysql
* nginx_fpm_supervisord: docker-compose with 2 containers: nginx & php-fpm in the same container, mysql

To understand the differences between the two approachs you can read those articles:
- [Dockerize a PHP project. Docker compose way](https://blog.forma-pro.com/dockerize-a-php-project-docker-compose-way-be0756d3bfa9)
- [Dockerize a PHP project. Supervisord approach](https://blog.forma-pro.com/dockerize-a-php-project-supervisord-approach-53860e8b4d9e)

# Warning
After installation the name of your admin dir must be 'admin'.
You can edit this value in the nginx config file (nginx_fpm/prestashop-nginx/prestashop-nginx.conf) 

To test:
```
./prestashopCli.sh --clone
./prestashopCli.sh -d nginx_fpm
cd nginx_fpm
docker-compose up
```

You can launch the installation of [your shop here](http://localhost:8080)
