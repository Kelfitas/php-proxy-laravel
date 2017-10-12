all: install

install:
	docker-compose up

run: ./proxy.php
	php -f ./proxy.php