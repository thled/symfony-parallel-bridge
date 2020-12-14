composer install # installs composer packages with composer.lock versions
composer update # updates composer packages
composer install --working-dir=tools/phpstan #install phpstan
tools/phpstan/vendor/bin/phpstan analyse src tests --configuration=config.neon #check project with phpstan
composer update --working-dir=tools/php-cs-fixer #install php-cs-fixer
tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --allow-risky=yes # run cs-fixer *has problems with php8 right now*
vendor/bin/simple-phpunit # run phpunit
