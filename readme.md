
Project for fixed asset accounting and depreciation for Czech accounting agencies/companies
=================

Nette Web Project
-------

- Web Project for Nette 3.1 requires PHP 8.0 and- MySQL 8.*

Installation
------------

Make directories `temp/` and `log/` writable. (chmod 775)

It is possible to run docker in .docker folder:
    - run docker docker compose -p bp up -d --build --force-recreate)


```bash
$ mv etc/config.local.neon.dist etc/config.local.neon
$ composer install
$ php bin/console doctrine:database:create # will take database name from config file
$ php bin/console migrations:migrate
```

Web Server Setup
----------------

For Apache or Nginx, setup a virtual host to point to the `www/` directory of the project and you
should be ready to go.

**It is CRITICAL that whole `app/`, `config/`, `log/` and `temp/` directories are not accessible directly
via a web browser. See [security warning](https://nette.org/security-warning).**
