.PHONY: tests

start-dev:
	npx yarn
	npx yarn dev
	docker-compose up -d --build
	docker exec -it library-php7.4 composer i
	docker exec -it library-php7.4 bin/console doctrine:migrations:migrate -n
	docker exec -it library-php7.4 bin/console doctrine:migrations:migrate -n --env=test
	docker exec -it library-php7.4 bin/console doctrine:fixtures:load -n
	@echo ""
	@echo "    WEB: http://library.localhost"
	@echo "    REST: http://library.localhost/api/v1/books"

stop:
	docker-compose down

tests:
	docker-compose up -d --build
	docker exec -it library-php7.4 bin/console doctrine:migrations:migrate -n --env=test
	docker exec -it library-php7.4 bin/phpunit

console:
	docker exec -it library-php7.4 bash
