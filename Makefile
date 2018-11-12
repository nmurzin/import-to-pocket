ENV=local
dir=${CURDIR}
project-server=import-to-pocket-server
project=import-to-pocket
image=php:7.2-cli

exec:
	docker run -it --rm --name $(project) -v "$(dir)":/var/www -w /var/www $(image) php index.php

serve:
	docker run -it --rm --name $(project-server) -v "$(dir)":/var/www -p 80:80 -w /var/www $(image) php -S 0.0.0.0:80

dump-autoload:
	docker run --rm --interactive --tty --volume $(dir):/app composer dump-autoload

composer-install:
	docker run --rm --interactive --tty --volume $(dir):/app composer install