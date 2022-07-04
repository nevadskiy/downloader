# Start app containers
up:
	docker-compose up -d

# Stop app containers
down:
	docker-compose down

# Run the testsuite
test:
	docker-compose run --rm app vendor/bin/phpunit

## CHECK BELOW

# Install the app
install: build vendor

# Build the app container
build:
	docker build -t app .

# Install app dependencies
composer.install:
	docker run --rm -it -v ${PWD}:/app app composer install

# Update app dependencies
composer.update:
	docker run --rm -it -v ${PWD}:/app app composer update

# Show outdated dependencies
composer.outdated:
	docker run --rm -it -v ${PWD}:/app app composer outdated

# Dump composer autoload
autoload:
	docker run --rm -it -v ${PWD}:/app app composer dump-autoload

# Generate a coverage report as html
coverage.html:
	docker run --rm -it -v ${PWD}:/app app vendor/bin/phpunit --coverage-html tests/report

# Generate a coverage report as text
coverage.text:
	docker run --rm -it -v ${PWD}:/app app vendor/bin/phpunit --coverage-text

# Coverage text alias
coverage: coverage.text

# Fix the code style
fix:
	docker run --rm -it -v ${PWD}:/app app vendor/bin/php-cs-fixer fix

# Run PHP server for file fixtures
server:
	docker run --rm -it -v ${PWD}:/app --publish 8888:8888 --expose 8888 app php -S localhost:8888 -t tests/Server index.php
