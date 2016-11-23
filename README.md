Redis Fixtures Bundle
===========

A Symfony Bundle to ease the import of dynamic Redis Fixtures

Installation
------------

The recommended way to install Redis Fixtures Bundle is through
[Composer](http://getcomposer.org/):

```bash
$ composer require lab5com/redis-fixtures-bundle
```


Usage
--------------
Create a PHP class where ever you like it (recommended location: AppBundle/DataFixtures/Redis)
which must have a filename that ends in *RedisFixture.php and implements RedisFixtureInterface.
Its getData() method should return a key -> value array.