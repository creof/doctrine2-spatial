Test environment
================

If you want to contribute to this library, you're welcome. This section will help you to prepare your development
environment.

How to test library?
--------------------

Doctrine library is available for MySQL and PostGreSQL. Be aware that MariaDB spatial functions does not returns the
same results than MySQL spatial functions. Then tests failed on MariaDB. So do not use MariaDB to test MySQL
abstraction.

How to test on MySQL?
---------------------

I suppose that composer and MySQL are already installed on your dev environment.

1. Create a role that can create database and locally connect with a password,
2. Create a phpunit.mysql.xml file copied from phpunit.mysql.xml.dist file,
3. Edit this phpunit.mysql.xml to change connection parameters.
4. run the command `composer test-mysql`

How to test on PostgreSQL?
--------------------------

I supposed that composer, PgSQL and its Postgis extension are installed.

1. Create a role that is a superuser because this user will create a database and create postgis extension,
2. Create a `phpunit.pgsql.xml` file copied from `phpunit.pgsql.xml.dist` file,
3. Edit this `phpunit.pgsql.xml` to change connection parameters.
4. run the command `composer test-pgsql`

How to test with the three PHP versions?
----------------------------------------

This library is available for PHP7.2, PHP7.3 and PHP7.4 versions.
So you have to test library with this three versions.

If you use an IDE like PHPStorm, you can create configurations to launch the six tests suite with the corresponding to:

* MySQL, PHP7.2 and PHPUnit 8.5
* PostgreSQL, PHP7.2 and PHPUnit
* MySQL, PHP7.3 and PHPUnit 8.5
* PostgreSQL, PHP7.3 and PHPUnit
* MySQL, PHP7.4 and PHPUnit 8.5
* PostgreSQL, PHP7.4 and PHPUnit

In this section, I described an easy way to switch PHP version via console. (But there is a lot of ways to do it.)

I suppose you have installed all php versions on your dev environment.

1. Download symfony binary,
2. Verify that PHP7.2,PHP7.3 and PHP7.4 are available:

.. code-block:: bash

     symfony local:php:list
    ┌─────────┬────────────────────────────────┬─────────┬─────────┬─────────────┬─────────┬─────────┐
    │ Version │           Directory            │ PHP CLI │ PHP FPM │   PHP CGI   │ Server  │ System? │
    ├─────────┼────────────────────────────────┼─────────┼─────────┼─────────────┼─────────┼─────────┤
    │ 7.1.30  │ C:\Users\alexandre\Php\php-7.1 │ php.exe │         │ php-cgi.exe │ PHP CGI │         │
    │ 7.2.25  │ C:\Users\alexandre\Php\php-7.2 │ php.exe │         │ php-cgi.exe │ PHP CGI │         │
    │ 7.3.12  │ C:\Users\alexandre\Php\php-7.3 │ php.exe │         │ php-cgi.exe │ PHP CGI │         │
    │ 7.4.1   │ C:\Users\alexandre\Php\php-7.4 │ php.exe │         │ php-cgi.exe │ PHP CGI │ *       │
    └─────────┴────────────────────────────────┴─────────┴─────────┴─────────────┴─────────┴─────────┘

3. Create a `.php-version` containing the PHP version to change php version

.. code-block:: bash

    echo 7.2 > .php-version

Now PHP 7.2 will be used each time you use one of this command:

.. code-block:: bash

    $ symfony php -v
    PHP 7.2.26 (cli) (built: Dec 17 2019 15:29:44) ( NTS MSVC15 (Visual C++ 2017) x64 )
    Copyright (c) 1997-2018 The PHP Group
    Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Xdebug v2.9.0, Copyright (c) 2002-2019, by Derick Rethans

    $ symfony composer

4. Download PHPUnit.phar for version 8: Go on https://phar.phpunit.de
5. You should now have a phpunit-8.phar and a phpunit-8.phar in your directory
6. This script launch the six test-suites:

.. code-block:: bash

    $ echo 7.2 > .php-version
    $ symfony php phpunit-8.phar --configuration phpunit.mysql.xml
    $ symfony php phpunit-8.phar --configuration phpunit.pgsql.xml
    $ echo 7.3 > .php-version
    $ symfony php phpunit-8.phar --configuration phpunit.mysql.xml
    $ symfony php phpunit-8.phar --configuration phpunit.pgsql.xml
    $ echo 7.4 > .php-version
    $ symfony php phpunit-8.phar --configuration phpunit.mysql.xml
    $ symfony php phpunit-8.phar --configuration phpunit.pgsql.xml
