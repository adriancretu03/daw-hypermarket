-include .env

DB_NAME ?= hypermarket
DB_USER ?= hypermarket
DB_PASS ?= hypermarket

.DEFAULT_GOAL := help
.PHONY: start stop restart logs create-db seed-db help

start: ## Build and start the containers
	docker compose up -d --build

stop: ## Stop the containers
	docker compose down

restart: ## Restart the containers
	docker compose restart

logs: ## Follow the web container logs
	docker compose logs -f web

create-db: ## Create the database schema
	docker compose exec -T db mysql --default-character-set=utf8mb4 -u$(DB_USER) -p$(DB_PASS) $(DB_NAME) < sql/schema.sql \
		&& echo "Schema created successfully." || (echo "Schema creation failed." && exit 1)

seed-db: ## Insert demo data
	docker compose exec -T db mysql --default-character-set=utf8mb4 -u$(DB_USER) -p$(DB_PASS) $(DB_NAME) < sql/seed.sql \
		&& echo "Seed data inserted successfully." || (echo "Seed insert failed." && exit 1)

help: ## Show this help
	@grep -hE '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'
