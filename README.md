# Doctrine2-Spatial
Doctrine2 multi-platform support for spatial types and functions. 
Currently MySQL and PostgreSQL with PostGIS are supported. 
Could potentially add support for other platforms if an interest is expressed.

## Current status
[![Build Status](https://travis-ci.org/creof/doctrine2-spatial.svg?branch=master)](https://travis-ci.org/Alexandre-T/doctrine2-spatial)
[![Code Climate](https://codeclimate.com/github/creof/doctrine2-spatial/badges/gpa.svg)](https://codeclimate.com/github/Alexandre-T/doctrine2-spatial)
[![Coverage Status](https://coveralls.io/repos/creof/doctrine2-spatial/badge.svg?branch=master&service=github)](https://coveralls.io/github/Alexandre-T/doctrine2-spatial?branch=master)
[![Downloads](https://img.shields.io/packagist/dm/creof/doctrine2-spatial.svg)](https://packagist.org/packages/creof/doctrine2-spatial)
[![Documentation Status](https://readthedocs.org/projects/doctrine2-spatial/badge/?version=latest)](https://doctrine2-spatial.readthedocs.io/en/latest/?badge=latest)

Documentation 
-------------

The [new documentation](https://doctrine2-spatial.readthedocs.io) explain how to:

* install this doctrine extension,
* configure this extension,
* create spatial entities,
* use spatial functions into your repositories,
* contribute (and test)

The documentation contains a glossary of all available types and all available spatial functions.

## Project origins
This useful project was created by Derek J. Lambert. 
Alexandre Tranchant forked it from [creof/doctrine2-spatial](https://github.com/creof/doctrine2-spatial) 
because project seems to be non-active since 2017.

The master release can be used, but be careful of backward incompatibility. 

## Developments in progress
This fork will upgrade this package to the last doctrine version and the [PHP supported versions](https://www.php.net/supported-versions.php). 
I would like to release the 2.0.0 version at the end of March.


Compatibility
-------------
### PHP
This doctrine extension is compatible with PHP 7.2, 7.3 and 7.4
Security fixes will follow the [PHP Roadmap](https://www.php.net/supported-versions.php).

### Doctrine dev version
This extension should be used with the actual doctrine stable version: 2.7
Continuous integration tests libraries with 2.8.x-dev version. We **ONLY** try to stay compatible with this version, 
currently. 

### MySQL 5.7 and 8.0
A lot of functions change their names between this two versions. The [MySQL 5.7 deprecated functions](https://stackoverflow.com/questions/60377271/why-some-spatial-functions-does-not-exists-on-my-mysql-server)
are not implemented.

### MariaDB 10
This version is **NOT** compatible with MariaDB version. Some spatial functions seems to work but their results are 
different from MySQL version (StContains function is a good example). You can contribute, but I suggest to avoid 
MySql and MariaDb servers, because of [their shortcomings and vulnerabilities](https://sqlpro.developpez.com/tutoriel/dangers-mysql-mariadb/).

### PostgreSQL
You should use PostgreSql server. This is a most powerful server and this is a "true" database server. It preserves data 
integrity and respect atomicity concepts. This spatial library is compatible with PostgreSql9.6, PostgreSql10 and 
PostgreSql11. I tested it with PostgreSql12. But I do not know how to install a PostgreSql 12 server on travis to be 
sure that library stay compatible. Be careful, this library is only tested with Postgis 2.5+. It is not tested with 
Postgis3.0, but feel free to contribute by updating the [travis configuration](./.travis.yml)
