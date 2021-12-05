
start-dev:
	npx yarn
	npx yarn dev
	docker-compose build
	docker-compose up -d
	docker exec -it library-php7.4 composer i
	docker exec -it library-php7.4 bin/console doctrine:fixtures:load -n
	@echo ""
	@echo "    WEB: http://library.localhost"
	@echo "    REST: http://library.localhost/api/v1/books"

stop:
	docker-compose down

console:
	docker exec -it library-php7.4 bash
