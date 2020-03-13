# Doctrine2-Spatial
Doctrine2 multi-platform support for spatial types and functions. 
Currently MySQL and PostgreSQL with PostGIS are supported. 
Could potentially add support for other platforms if an interest is expressed.

## Project origins
This useful project was created by Derek J. Lambert. 
Alexandre Tranchant forked it from [creof/doctrine2-spatial](https://github.com/creof/doctrine2-spatial) 
because project seems to be unactive since 2017.

The master release can be used, but be careful, the code coverage is false, and only 30% of spatial functions are tested. (Spatials Functions are only implemented with class containing only properties. So, they have zero code line and code coverage said they are fully covered, but it's false. To avoid this, all spatial functions are rebuild in [OGC branch](https://github.com/Alexandre-T/doctrine2-spatial/tree/ogc). This branch is under development

## Developments in progress
This fork will upgrade this package to the last doctrine version and the [PHP supported versions](https://www.php.net/supported-versions.php). Developments are done under [ogc branch](https://github.com/Alexandre-T/doctrine2-spatial/tree/ogc) because of backward incompatibility, we suggest to not use this fork for the moment. I would like to release the 2.0.0 version at the end of March.

Currently I'm searching help to configure Travis and configure PostgreSQL tests works on my dev environment but fails on Travis because of user connection. I'm searching ISO/IEC 13249-3:2016 documentation. I'm only working with [OGC Standard](https://www.ogc.org/standards/sfs)


## Documentation will be upgraded 

I will provide a lot of example, essentialy with a symfony project.
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
  


 
 
