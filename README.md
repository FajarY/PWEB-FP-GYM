# PWEB-FP
___

# Setup
- Install Docker

- Download npm
    - `sudo apt install nodejs`
    - `sudo apt install npm`
    - `npm install`

- Download composer
    - `sudo add-apt-repository ppa:ondrej/php -y`
    - `sudo apt update`
    - `sudo apt install php8.3 php8.3-curl php8.3-fpm php8.3-pgsql`
    - `sudo cp ./configs/php.ini /etc/php/8.3/fpm/php.ini`
    - `wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet`
    - `sudo mv composer.phar /usr/local/bin/composer`
    - `composer upgrade`
    - `composer dump-autoload`
    - `service php8.3-fpm restart`
___

# Editting
- When editting and referencing a tailwind class be sure to run this. Its a tailwindcss compiler that runs in the background
- `./editting.sh`
___

# Run
- Run docker daemon
- Be sure to use the updated .env, download it in notion and put it in root directory /
- Be sure to upgrade all packages
- `./deploy.sh`