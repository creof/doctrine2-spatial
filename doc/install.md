# Installation

## Setup/Installation

Use these instructions if you're using Doctrine with Symfony2 , otherwise the OrmTest.php test class shows their use with Doctrine alone.

Require the `alexandret/spatial2-doctrine` package in your composer.json and update
your dependencies.

    $ composer require alexandret/doctrine2-spatial
    
## composer.json

    "require": {
    	...
        "alexandret/doctrine2-spatial": "^2.0"
    
You will also have to change the version requirement of doctrine to at least 2.7:

        "doctrine/orm": ">=2.7",
