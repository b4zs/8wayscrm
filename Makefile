#todo: figure out how to disable it in production environment.

PHP = php
MEM = -d memory_limit=256M
CON = app/console
ENV = dev
GULP = node node_modules/gulp/bin/gulp.js

update:
	make composer
	make cc
	make cc-prod
	make assets
	make db-migrate
	make jsroutes

build:
	make composer
	make cc
	make assets
	make db-migrate-from-scratch
	make jsroutes



usage:
	@echo ""
	@echo "usage:"
	@echo ""
	@echo "* make assets:    update application assets"
	@echo "* make jsroutes:  generate js routes"
	@echo "* make cc:        clear application and php cache"

composer:
	composer install --no-scripts


assets:
	$(PHP) $(MEM) $(CON) assets:install --symlink --env=$(ENV)

assetic:
	$(PHP) $(MEM) $(CON) assetic:dump

db-migrate:
	$(PHP) $(MEM) $(CON)  doctrine:migrations:migrate --no-interaction

db-migrate-from-scratch:
	$(PHP) $(MEM) $(CON)  doctrine:schema:drop --full-database --force
	make db-migrate
	$(PHP) $(MEM) $(CON)  doctrine:fixtures:load --append --fixtures=src/Application/UserBundle/DataFixtures/UserFixture.php

db-migration-generate:
	#make cc
	#$(PHP) $(MEM) $(CON)  doctrine:schema:drop --full-database --force
	$(PHP) $(MEM) $(CON)  doctrine:migrations:migrate --no-interaction
	$(PHP) $(MEM) $(CON)  doctrine:migrations:diff
	$(PHP) $(MEM) $(CON)  doctrine:migrations:migrate --no-interaction


jsroutes:
	#$(PHP) $(CON) fos:js-routing:dump --env=$(ENV)
	echo 'no fos:js-routing installed yet'

cc:
	#$(PHP) $(MEM) $(CON) cache:clear --no-warmup --env=$(ENV)
	rm -rf app/cache/dev

cc-prod:
	#$(PHP) $(MEM) $(CON) cache:clear --no-warmup --env=prod
	rm -rf app/cache/prod

xdebug_cli_on:
	XDEBUG_CONFIG="remote_enable=1 remote_mode=req remote_port=9000 remote_host=127.0.0.1 remote_connect_back=0"


xdebug_cli_off:
	$(shell `export XDEBUG_CONFIG=""`)

#eg. make phpunit-app tests=src/Provider/TravelportBundle/Tests/Air/AirServiceTest.php
phpunit-app:
	php vendor/phpunit/phpunit/phpunit  --bootstrap app/bootstrap.php.cache --configuration app/phpunit.xml $(tests)

db-diff:
	make cc
	$(PHP) $(MEM) $(CON)  doctrine:migrations:migrate --no-interaction
	$(PHP) $(MEM) $(CON)  doctrine:migrations:diff

run:
	$(PHP) -S localhost:8000 -t web/

q:
	$(PHP) $(MEM) $(CON) jms-job-queue:run -r 900 --idle-time 1 --env=$(ENV) -vv

qgrun:
	cd qgfrontend && npm start