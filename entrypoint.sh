#!/bin/sh

php bin/console doctrine:migrations:migrate --quiet
php bin/console doctrine:fixtures:load --no-interaction