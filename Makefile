#Makefile
SHELL := /bin/bash

ex-tests: 
	docker exec -it tryingtech-slim-php-fpm-1 ./vendor/bin/phpunit tests/TryingTest.php
test: 
	docker exec -it tryingtech-slim-php-fpm-1 ./vendor/bin/phpunit tests/AppTest.php
build-pull: 
	docker build --tag ghcr.io/botlane-feeder/tryingtech-slim:latest --tag ghcr.io/botlane-feeder/tryingtech-slim:0.2.0 --file configs/php-fpm/Dockerfile . ; \
	docker push ghcr.io/botlane-feeder/tryingtech-slim:latest ; \
	docker push ghcr.io/botlane-feeder/tryingtech-slim:0.2.0
version:
	source .env ; \
        echo ${VERSION}
help:
	echo "Help section WIP"
