FROM ubuntu:22.04

WORKDIR /var/www

RUN apt-get update --fix-missing
RUN apt-get install -y software-properties-common
RUN rm -rf /var/lib/apt/lists/*
RUN add-apt-repository ppa:ondrej/php -y
RUN apt-get update
RUN apt-get install --fix-missing nginx php8.3 php8.3-fpm php8.3-pgsql unzip wget -y
RUN rm -rf /var/lib/apt/lists/*
RUN apt-get clean
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet && mv composer.phar /usr/local/bin/composer

RUN mkdir php-website

WORKDIR php-website

COPY ./composer.json composer.json
COPY ./composer.lock composer.lock
COPY ./public public
COPY ./src src
COPY ./configs/nginx.conf /etc/nginx/nginx.conf
COPY ./Dockerfile/run.sh run.sh

RUN chmod +x run.sh
RUN composer upgrade

ENTRYPOINT [ "./run.sh" ]