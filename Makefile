init:
	if [ ! -f .env ]; then cp .env.example .env; fi
	docker-compose up -d --build
	docker-compose run --rm composer clear-cache
	docker-compose run --rm composer install
	docker-compose exec php php artisan migrate
	docker-compose exec php php artisan db:seed

up:
	docker-compose up -d

down:
	docker-compose down
