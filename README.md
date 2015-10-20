# Doctrine2-Spatial

Doctrine2 multi-platform support for spatial types and functions. Currently MySQL and PostgreSQL with PostGIS are supported.

Documentation can be found at [here](./doc/index.md)

## composer.json

    "require": {
    	...
        "creof/doctrine2-spatial": ">=0.1"

You will also have to change the version requirement of doctrine to at least 2.1:

        "doctrine/orm": ">=2.1",
