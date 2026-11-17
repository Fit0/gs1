# fit0/gs1 developer commands. Run `make` to list them. Everything runs in Docker, for the PHP version in PHP.
#
#   make qa            all the checks, on PHP 8.4
#   make qa PHP=8.3    the same on another version of the matrix (run `make update PHP=8.3` first)

PHP   ?= 8.4
IMAGE := fit0-gs1:php$(PHP)
RUN   := docker run --rm -t -v "$(CURDIR)":/app -v fit0-gs1-composer:/root/.composer $(IMAGE)

.DEFAULT_GOAL := help
.PHONY: help image install update validate test stan cs fix rector qa sh

help: ## List available commands
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[33m%-10s\033[0m %s\n", $$1, $$2}'

image: ## Build the image for PHP (default 8.4)
	docker build --build-arg PHP_VERSION=$(PHP) --tag $(IMAGE) docker

install: image ## Install the dependencies for PHP
	$(RUN) composer install --no-interaction --no-progress

update: image ## Resolve the dependencies again for PHP (the lock file is not committed)
	$(RUN) composer update --no-interaction --no-progress

validate: image ## Validate composer.json
	$(RUN) composer validate --strict

test: ## Pest
	$(RUN) vendor/bin/pest

stan: ## PHPStan (max level)
	$(RUN) vendor/bin/phpstan analyse --memory-limit=1G

cs: ## PHP-CS-Fixer, dry run
	$(RUN) vendor/bin/php-cs-fixer fix --dry-run --diff

rector: ## Rector, dry run
	$(RUN) vendor/bin/rector process --dry-run

fix: ## Apply Rector and PHP-CS-Fixer
	$(RUN) vendor/bin/rector process
	$(RUN) vendor/bin/php-cs-fixer fix

qa: validate cs rector stan test ## All the checks. Must pass before proposing a commit.

sh: ## Shell in the container
	docker run --rm -it -v "$(CURDIR)":/app -v fit0-gs1-composer:/root/.composer $(IMAGE) sh
