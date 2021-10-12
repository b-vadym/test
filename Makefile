DOCKER_COMPOSE = docker-compose
EXEC_PHP       = $(DOCKER_COMPOSE) exec  php
SYMFONY        = $(EXEC_PHP) bin/console
COMPOSER       = $(EXEC_PHP) composer
YARN           = $(DOCKER_COMPOSE) run --rm node yarn

##
## Project main
## -----------------------
.PHONY: start
start: ## Start docker compose
	$(DOCKER_COMPOSE) up --remove-orphans --no-recreate --detach

.PHONY: down
down: ## Down docker compose
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --remove-orphans

.PHONY: clear-cache
clear-cache: ## Remove symfony cache and logs
	rm -rf var/logs var/cache

.PHONY: cache-warmup
cache-warmup: clear-cache vendor ## Symfony cache warmup
	$(SYMFONY) cache:warmup

.PHONY: install ## install project
install: clear-cache start vendor cache-warmup db assets

.PHONY: reinstall ## Reinstall project
reinstall: down install

vendor: composer.lock ## Install vendor
	$(COMPOSER) install

.PHONY: db
db: vendor clear-cache cache-warmup start ## Recreate db and load fixtures
	$(EXEC_PHP) bin/mysql-check-alive
	$(SYMFONY) doctrine:database:drop --force --quiet
	$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:schema:update --force
	$(SYMFONY) doctrine:migrations:sync-metadata-storage --no-interaction
	$(SYMFONY) doctrine:migrations:version --add --all --no-interaction
	$(SYMFONY) doctrine:fixtures:load --append

node_modules: start yarn.lock ## install node modules
	$(YARN) install

.PHONY: assets
assets: vendor start cache-warmup node_modules ## Installing assets
	$(SYMFONY) assets:install --symlink
	$(YARN) encore dev
##
## help
## -----------------------

.PHONY: help
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-24s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## /[33m/' && printf "\n"

.DEFAULT_GOAL := help