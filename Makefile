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

# Run the testsuite
test:
	docker run --rm -it -v ${PWD}:/app app vendor/bin/phpunit

# Generate a coverage report as html
coverage-html:
	docker run --rm -it -v ${PWD}:/app app vendor/bin/phpunit --coverage-html tests/report

# Generate a coverage report as text
coverage-text:
	docker run --rm -it -v ${PWD}:/app app vendor/bin/phpunit --coverage-text

# Coverage text alias
coverage: coverage-text

# Fix the code style
fix:
	docker run --rm -it -v ${PWD}:/app app vendor/bin/php-cs-fixer fix
