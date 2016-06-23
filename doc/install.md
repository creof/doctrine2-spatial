# Installation

## Setup/Installation

Use these instructions if you're using Doctrine with Symfony2 , otherwise the OrmTest.php test class shows their use with Doctrine alone.

Require the `CrEOF/spatial2-doctrine` package in your composer.json and update
your dependencies.

    $ composer require CrEOF/doctrine2-spatial
    
## composer.json

    "require": {
    	...
        "CrEOF/doctrine2-spatial": "^0.1"
    
You will also have to change the version requirement of doctrine to at least 2.1:

        "doctrine/orm": ">=2.1",

## Configuration
Read [configuration]() to configure the extension for Symfony
