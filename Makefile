.DEFAULT_GOAL := all
.PHONY: help
.SILENT:

URL?=""
PAGES?=100

## Make and run app in Docker
all: docker-install docker-run


## Build Docker compose
docker-build:
	docker-compose build


## Run app in Docker
docker-run:
	docker-compose run backend ./bin/app sitemapper --pages $(PAGES) -- $(URL)

## Run locally
run: install
	./bin/app sitemapper -- $(URL) --pages $(PAGES)


# Helper to run interactively
docker-bash:
	docker-compose run backend bash


## Run unit tests (Docker)
docker-test: docker-install
	docker-compose run backend ./vendor/bin/phpunit -c tests/phpunit.xml

## Runs unit tests
test: install
	php vendor/bin/phpunit -c tests/phpunit.xml


# Helpers


## Install dependencies (Docker)
docker-install: docker-build
	# This will run as root, but it's fine for this purpose
	docker-compose run backend curl https://getcomposer.org/installer | php -- --quiet
	docker-compose run backend ./composer.phar install --optimize-autoloader --no-interaction

## Install dependencies
install: composer.phar
	./composer.phar install --optimize-autoloader --no-interaction

# Install composer (Makefile lockfile ./composer.phar)
composer.phar:
	curl https://getcomposer.org/installer | php -- --quiet

## Cleanup composer and vendor folder
clean: docker-stop
	rm -rf composer.phar vendor/


## Prints this help :D
help:
	@awk -v skip=1 \
		'/^##/ { sub(/^[#[:blank:]]*/, "", $$0); doc_h=$$0; doc=""; skip=0; next } \
		 skip  { next } \
		 /^#/  { doc=doc "\n" substr($$0, 2); next } \
		 /:/   { sub(/:.*/, "", $$0); printf "\033[34m%-30s\033[0m\033[1m%s\033[0m %s\n\n", $$0, doc_h, doc; skip=1 }' \
		$(MAKEFILE_LIST)
