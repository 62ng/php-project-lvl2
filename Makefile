install:
	composer install
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
gendiff:
	php ./bin/gendiff
test:
	composer exec --verbose phpunit tests