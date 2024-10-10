ex-tests: 
	docker exec -it tryingtech-slim-php-fpm-1 ./vendor/bin/phpunit tests/TryingTest.php
test: 
	docker exec -it tryingtech-slim-php-fpm-1 ./vendor/bin/phpunit tests/AppTest.php

