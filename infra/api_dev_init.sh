#!/bin/bash -e

apt-get update && apt-get install -y\
    zlib1g-dev\
    libzip-dev\
    unzip\
    git\
    wget\
  	--no-install-recommends && rm -r /var/lib/apt/lists/*

wget https://get.symfony.com/cli/installer -O - | bash &&\
    wget http://pear.php.net/go-pear.phar &&\
    php go-pear.phar &&\
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony

docker-php-ext-install\
    pdo_mysql\
    pdo

pecl install\
    xdebug-2.8.0beta2\
    && docker-php-ext-enable xdebug

tee /usr/local/etc/php/php.ini <<EOF
xdebug.remote_enable=1
xdebug.remote_connect_back=1
xdebug.remote_host=host.docker.internal
xdebug.remote_port=9000
EOF

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"\
    && php composer-setup.php --install-dir=/usr/bin --filename=composer\
    && php -r "unlink('composer-setup.php');"

mkdir /api && chown www-data:www-data -R /api