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
    - `sudo apt install php8.3 php8.3-curl php8.3-fpm php8.3-pgsql php8.3-curl`
    - `sudo cp ./configs/php.ini /etc/php/8.3/fpm/php.ini`
    - `wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet`
    - `sudo mv composer.phar /usr/local/bin/composer`
    - `composer upgrade`
    - `composer dump-autoload`
    - `service php8.3-fpm restart`

- Download imagemagick
    - `apt-get install php8.3-imagick imagemagick -y`
    - Add/Enable This to php.ini
        - `extension=imagick.so`
        - `extension=curl`
    - To Enable VSCode Intelephense for Imagick, visit https://stackoverflow.com/questions/62258598/undefined-type-imagick-in-vscodes-intelephense
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
___

# Tests
- Deploy Application
- Get JWT Token and save it to .env.test, it will be used for testing, since google authentication cannot be automized
    - Go to http://localhost:8080/auth
    - Sign in with google account
    - It will redirect to /verify
    - Open website console, and get `token` from Application -> Cookie
    - Paste the cookie to .env.test
- Run test with `npm run test`
- If the /api/auth/verify test is succeed and you want to run test again, run `node tests/reset.js`. This will reset all data on Database
- If reset is successfull, go to Step 2 Again (Get JWT...)