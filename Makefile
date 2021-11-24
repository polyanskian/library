demo:
	docker-compose up -d --build
	docker exec -t library-php7.4 composer i
	echo 'DATABASE_URL="postgresql://root:1234567890@library-postgres-12:5432/library?serverVersion=12&charset=utf8"' > ./.env.local
	docker exec -it library-php7.4 bin/console doctrine:migrations:migrate
	
	npx yarn
	npx yarn build
	
	docker exec -it library-php7.4 bin/console fos:user:create test test@test.test 1234567890
	docker exec -it library-php7.4 bin/console fos:user:promote test ROLE_ADMIN
