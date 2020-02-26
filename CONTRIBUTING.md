Contributing
============

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
