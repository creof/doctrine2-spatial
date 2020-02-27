Contributing
============

## Which step to submit a new function

1 - Create the new function into the CrEOF\Spatial\ORM\Query\AST\Functions namespace
2 - Create the class test into the CrEOF\Spatial\ORM\Query\AST\Functions
3 - Declare the new function into the [OrmTestCase](./tests/CrEOF/Spatial/Tests/OrmTestCase.php) class test
4 - Launch the test ;) (Read the last paragraph of this page, to know how to config your dev environment)
5 - Update the [MySQL](doc/mysql.md) or [PostGreSQL](doc/postgresql.md) description
6 - Add your function into the configuration [symfony chapter](doc/configuration/configuration.md) 

## Code quality
Quality of code is auto-verified by php-cs-fixer, php code sniffer and php mess detector.

Before a commit, launch the quality script:

```bash
composer check-quality-code
```

You can launch PHPCS-FIXER only with:
```bash
composer phpcsfixer
```

You can launch PHP Code Sniffer only with:
```bash
composer phpcs
```

You can launch PHP Mess Detector only with:
```bash
composer phpmd
```

## Tests

This [page](./doc/test.md) describes how to prepare your test environment and launch the six test-suites:
1. Testsuite for PHP7.2 and MySQL environments executed by Phpunit8
2. Testsuite for PHP7.2 and PostgreSQL environments executed by Phpunit8
3. Testsuite for PHP7.3 and MySQL environments executed by Phpunit9
4. Testsuite for PHP7.3 and PostgreSQL environments executed by Phpunit9
5. Testsuite for PHP7.4 and MySQL environments executed by Phpunit9
6. Testsuite for PHP7.4 and PostgreSQL environments executed by Phpunit9
