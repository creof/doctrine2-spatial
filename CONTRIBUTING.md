Contributing
============

## Which are necessary steps to create a new function?

It's pretty easy to create a new function.

If your function is described in the [OGC Standards]() or in the [ISO](), the class implementing the function shall be
create in the [lib\CrEOF\Spatial\ORM\Query\AST\Functions\Standard](./lib/CrEOF/Spatial/ORM/Query/AST/Functions/Standard) 
repository.

Create a new class. It's name shall be the same than the function name in camel case. So, it shall begin with ST.
As example, if you want to create the ST_X function, your class shall be named StX. 

If your spatial function is not described in the OGC Standards nor in the ISO, your class should be prefixed by Sp 
(specific). If your class is specific to MySql, you shall create it in the 
[lib\CrEOF\Spatial\ORM\Query\AST\Functions\MySQL](./lib/CrEOF/Spatial/ORM/Query/AST/Functions/MySql) directory.
If your class is specific to PostgreSQL, you shall create it in the 
[lib\CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSQL](./lib/CrEOF/Spatial/ORM/Query/AST/Functions/PostgreSql) directory.
If your class is not described in the OGC Standards nor in the ISO norm, but exists in MySQL and in PostgreSQL, accepts
the same number of arguments and returns the same results (which is rarely the case), then you shall create it in the 
[lib\CrEOF\Spatial\ORM\Query\AST\Functions\Common](./lib/CrEOF/Spatial/ORM/Query/AST/Functions/Common) directory. 

Now you know where to create your class, it should extends AbstractSpatialDQLFunction and you have to implement four functions:
1. ```getFunctionName()``` shall return the spatial function name,
2. ```getMaxParameter()``` shall return the maximum number of arguments accepted by the function name,
3. ```getMinParameter()``` shall return the minimum number of arguments accepted by the function name,
4. ```getPlatforms()``` shall return an array of each platform accepting this function.

As example, if the new spatial function exists in PostgreSQL and in MySQL, getPlatforms() should be like this:
```php 
    /**
     * Get the platforms accepted.
     *
     * @return string[] a non-empty array of accepted platforms
     */
    protected function getPlatforms(): array
    {
        return ['postgresql', 'mysql'];
    }
``` 

Do not hesitate to copy and paste the implementing code of an existing spatial function.

If your function is more specific and need to be parse, you can overload the parse method.
The PostgreSQL [SnapToGrid](./lib/CrEOF/Spatial/ORM/Query/AST/Functions/PostgreSql/SpSnapToGrid.php) is a good example.

## Which are necessary steps to test your function?
1 - Create the class test into the CrEOF\Spatial\ORM\Query\AST\Functions\*\ directory (replace * by Common, MySql, PostgreSql or Standard)
2 - Declare the new function into the [OrmTestCase](./tests/CrEOF/Spatial/Tests/OrmTestCase.php) class test
3 - Launch the test ;) (Read the last paragraph of this page, to know how to config your dev environment)
4 - Update the [standard](doc/standard/index.md), [MySQL](doc/mysql/index.md) or [PostGreSQL](doc/postgresql/index.md) description
5 - Add your function into the [symfony configuration chapter](doc/configuration.md) 

## Code quality
Quality of code is auto-verified by php-cs-fixer, php code sniffer and php mess detector.

Before a commit, launch the quality script:

```bash
composer check-quality-code
```

You can launch PHPCS-FIXER to fix errors with:
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
