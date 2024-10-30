
Project for fixed asset accounting and depreciation for Czech accounting agencies/companies
=================

Nette Web Project
-------

- Web Project for Nette 3.1 requires PHP 8.0 and MySQL 8.*

Installation
------------


It is possible to run docker in .docker folder:

- run:   docker compose -p bp up -d --build --force-recreate

```bash

$ mv config/local.neon.dist config/local.neon (copy file and insert connection config to DB)
$ composer install (inside docker: docker exec pos composer install)
$ php bin/console doctrine:database:create # will take database name from config file (in docker is created)
$ php bin/console migrations:migrate

Make directories `var/temp` and `var/log` writable. (sudo chmod -R 777 <folder>)
```

Web Server Setup
----------------

For Apache or Nginx, setup a virtual host to point to the `www/` directory of the project and you
should be ready to go.

**It is CRITICAL that whole `app/`, `config/`, `log/` and `temp/` directories are not accessible directly
via a web browser. See [security warning](https://nette.org/security-warning).**
