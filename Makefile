ENV=local
dir=${CURDIR}
project=import-to-pocket

exec:
    docker run -it --rm --name $(project) -v "$(dir)":/var/www -w /var/www php:7.2-cli php -S 0.0.0.0:80
	docker run -it --rm --name $(project) -v "$(dir)":/var/www -w /var/www php:7.2-cli php index.php

dump-autoload:
	docker run --rm --interactive --tty --volume $(dir):/app composer dump-autoload

composer-install:
	docker run --rm --interactive --tty --volume $(dir):/app composer install