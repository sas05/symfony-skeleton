Application
========================

Requirements
------------

  * PHP 7.1.3 or higher;
  * PDO-SQLite PHP extension enabled;
  * and the [usual Symfony application requirements][1].

Installation
------------

Execute this command to install the project:

```bash
$ composer install
```

Usage
-----

There's no need to configure anything to run the application. Just execute this
command to run the built-in web server and access the application in your
browser at <http://localhost:8000>:

```bash
$ cd thompson/
$ php bin/console server:run
```

Database
--------
```bash
$ cd thompson/
$ php bin/console doctrine:schema:create
```

Fixtures
--------

```bash
$ cd thompson/
$ php bin/console doctrine:fixtures:load --force
```

Tests
-----

Execute this command to run tests:

```bash
$ cd thompson/
$ ./vendor/bin/simple-phpunit
```
Docker
------

```bash
$ cd thompson/laradock
$ docker-compose up -d nginx
```
