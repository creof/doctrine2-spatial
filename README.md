# Doctrine2-Spatial
Doctrine2 multi-platform support for spatial types and functions. 
Currently MySQL and PostgreSQL with PostGIS are supported. 
Could potentially add support for other platforms if an interest is expressed.

## Project origins
This useful project was created by Derek J. Lambert. 
Alexandre Tranchant forked it from [creof/doctrine2-spatial](https://github.com/creof/doctrine2-spatial) 
because project seems to be unactive since 2017.

## Developments in progress
This fork will upgrade this package to the last doctrine version and the [PHP supported versions](https://www.php.net/supported-versions.php).

Feel free to [contribute](./CONTRIBUTING.md)!

## Current status
[![Build Status](https://travis-ci.org/Alexandre-T/doctrine2-spatial.svg?branch=master)](https://travis-ci.org/Alexandre-T/doctrine2-spatial)
[![Code Climate](https://codeclimate.com/github/Alexandre-T/doctrine2-spatial/badges/gpa.svg)](https://codeclimate.com/github/Alexandre-T/doctrine2-spatial)
[![Coverage Status](https://coveralls.io/repos/Alexandre-T/doctrine2-spatial/badge.svg?branch=master&service=github)](https://coveralls.io/github/Alexandre-T/doctrine2-spatial?branch=master)
[![Downloads](https://img.shields.io/packagist/dm/Alexandre-T/doctrine2-spatial.svg)](https://packagist.org/packages/Alexandre-T/doctrine2-spatial)

### Documentation
The documentation branch is under construction and will use [ReadTheDocs](https://www.readthedocs.io/)

Currently, the documentation is splitted into some files.
1. the [core of documentation](./doc/index.md), 
2. the [needed installation steps](./doc/install.md),
3. if your using symfony framework, the [configuration page](./doc/configuration.md) explains how to configure your 
symfony application, and how to configure the types and the spatial functions that you want to use in your application.
Do not forget, that it is not optimal to declare every types and every functions if you do not need them. 
4. the [entity page](./doc/entity.md) describes how to create an ORM entity with spatial columns
5. the standard, postgresql and mysql contains a description of some of the implementable method 

Compatibility
-------------
###PHP
This package to the last doctrine version and the [PHP supported versions](https://www.php.net/supported-versions.php).

###Doctrine dev version
This version should be used with the actual doctrine stable version: 2.7
Continuous integration tests libraries does not implements the 2.8.x-dev version. It should change very soon. 
I try to be compatible with this version.

Continuous integration tests libraries with 2.8.x-dev version. We **DO NOT* try to be compatible with this version, 
currently. There is too much difference between interface declarations.

###MySQL 5.7 and 8.0
A lot of functions change their names between this two versions. The [MySQL 5.7 deprecated functions](https://stackoverflow.com/questions/60377271/why-some-spatial-functions-does-not-exists-on-my-mysql-server)
are not implemented.

###MariaDB 10
This version is NOT compatible with MariaDB version. Some spatial functions seems to work but their results are 
different from MySQL version (StContains function is a good example). I suggest to avoid MySql and MariaDb servers.

###PostgreSQL
You should use PostgreSql server. This is a most powerful server and this is a "true" database server. It preserves data 
integrity and respect atomicity concepts. This spatial library is compatible with PostgreSql9.6, PostgreSql10 and 
PostgreSql11. I tested it with PostgreSql12. But I do not know how to install a PostgreSql 12 server on travis to be 
sure that library stay compatible. Be careful, this library is only tested with Postgis 2.5+. It is not tested with 
Postgis3.0

If someone knows how to implements PostgreSql12 and Postgis3.0 on travis, feel free to edit travis.yml file and push a 
request on github!
  


 
 