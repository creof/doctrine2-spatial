Contributing
************

Documentation
=============

This documentation is done with sphinx. All documentation are stored in the ``docs`` directory. To contribute to this
documentation (and fix the lot of typo), you need to install python, sphinx and the "readthedocs" template.

1. Fork this project,
2. Locally clone your forked project,
3. Edit files in the ``docs`` directory
4. Launch the ``make html``
5. Verify that documentation is improved
6. Commit your contribution with an explicit message
7. Push your commit on your forked project,
8. Do a pull request on your forked project to the Alexandre-T/doctrine2-spatial project

Source code
===========

How to create a new function?
-----------------------------

It's pretty easy to create a new function with a few lines code is sufficient.

Where to store your class?
^^^^^^^^^^^^^^^^^^^^^^^^^^
If your function is described in the `OGC Standards`_ or in the `ISO/IEC 13249-3`_, the class implementing the function
**shall** be create in the lib/CrEOF/Spatial/ORM/Query/AST/Functions/`Standard directory`_.

If your spatial function is not described in the OGC Standards nor in the ISO, your class should be prefixed by Sp
(specific). If your class is specific to MySql, you shall create it in the
lib/CrEOF/Spatial/ORM/Query/AST/Functions/`MySql directory`_.
If your class is specific to PostgreSQL, you shall create it in the
lib/CrEOF/Spatial/ORM/Query/AST/Functions/`PostgreSql directory`_.
If your class is not described in the OGC Standards nor in the ISO norm, but exists in MySQL and in PostgreSQL, accepts
the same number of arguments and returns the same results (which is rarely the case), then you shall create it in the
lib/CrEOF/Spatial/ORM/Query/AST/Functions/`Common directory`_.

Which name for your function?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Create a new class. It's name shall be the same than the function name in camel case prefixed with ``St`` or ``Sp``.
The standards are alive, they can be updated at any time. Regulary, new spatial function are defined by consortium. So,
to avoid that a new standardized function as the same name from an existing function, the ``St`` prefix is reserved to
already standardized function.

If your function is described in the `OGC Standards`_ or in the `ISO/IEC 13249-3`_, the prefix shall be ``St`` else your
class shall be prefixed with ``SP``.
As example, if you want to create the spatial ``ST_Z`` function, your class shall be named ``StZ`` in the
`Standard directory`_.
If you want to create the `ST_Polygonize`_ PostgreSql function which is not referenced in the OGC nor in,
then you shall name your class ``SpPolygonize`` and store them in the `PostgreSql directory`_.

Which method to implements?
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Now you know where to create your class, it should extends ``AbstractSpatialDQLFunction`` and you have to implement four functions:

1. ``getFunctionName()`` shall return the SQL function name,
2. ``getMaxParameter()`` shall return the maximum number of arguments accepted by the function,
3. ``getMinParameter()`` shall return the minimum number of arguments accepted by the function,
4. ``getPlatforms()`` shall return an array of each platform accepting this function.

As example, if the new spatial function exists in PostgreSQL and in MySQL, ``getPlatforms()`` should be like this:

.. code-block:: php

    <?php

    // ...

    /**
     * Get the platforms accepted.
     *
     * @return string[] a non-empty array of accepted platforms
     */
    protected function getPlatforms(): array
    {
        return ['postgresql', 'mysql'];
    }

Do not hesitate to copy and paste the implementing code of an existing spatial function.

If your function is more specific and need to be parse, you can overload the parse method.
The PostgreSQL `SnapToGrid`_ can be used as example.

All done! Your function is ready to used, but, please, read the next section to implement tests.

How to test your new function?
------------------------------

Please, create a functional test in the same way. You have a lot of example in the `functions test directory`_.

Setup
^^^^^

Here is an example of setup, each line is commented to help you to understand how to setup your test.

.. code-block:: php

    <?php

    use CrEOF\Spatial\Exception\InvalidValueException;
    use CrEOF\Spatial\Exception\UnsupportedPlatformException;
    use CrEOF\Spatial\Tests\Helper\PointHelperTrait;
    use CrEOF\Spatial\Tests\OrmTestCase;
    use Doctrine\DBAL\DBALException;
    use Doctrine\ORM\ORMException;

    /**
     * Foo DQL functions tests.
     * Thes tests verify their implementation in doctrine spatial.
     *
     * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
     * @license https://alexandre-tranchant.mit-license.org MIT
     *
     * Please prevers the three above annotation.
     *
     * Group is used to exclude some tests on some environment.
     * Internal is to avoid the use of the test outer of this library
     * CoversDefaultClass is to avoid that your test covers other class than your new class
     *
     * @group dql
     *
     * @internal
     * @coversDefaultClass
     */
    class SpFooTest extends OrmTestCase
    {
        // To help you to create some geometry, I created some Trait.
        // use it to be able to call some methods which will store geometry into your database
        // In this example, we use a trait that will create some points.
        use PointHelperTrait;

        /**
         * Setup the function type test.
         *
         * @throws DBALException                when connection failed
         * @throws ORMException                 when cache is not set
         * @throws UnsupportedPlatformException when platform is unsupported
         */
        protected function setUp(): void
        {
            //If you create point entity in your test, you shall add the line above or the **next** test will failed
            $this->usesEntity(self::POINT_ENTITY);
            //If the method exists in mysql, You shall test it. Comment this line if function does not exists on MySQL
            $this->supportsPlatform('mysql');
            //If the method exists in postgresql, You shall test it. Comment this line if function does not exists on PostgreSql
            $this->supportsPlatform('postgresql');

            parent::setUp();
        }

        /**
         * Test a DQL containing function to test in the select.
         *
         * @throws DBALException                when connection failed
         * @throws ORMException                 when cache is not set
         * @throws UnsupportedPlatformException when platform is unsupported
         * @throws InvalidValueException        when geometries are not valid
         *
         * @group geometry
         */
        public function testSelectSpBuffer()
        {
            //The above protected method come from the point helper trait.
            $pointO = $this->createPointO();
            //Please do not forget to flush and clear cache
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear();

            //We create a query using your new DQL function SpFoo
            $query = $this->getEntityManager()->createQuery(
                'SELECT p, ST_AsText(SpFoo(p.point, :p) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p'
            );
            //Optionnaly, you can use parameter
            $query->setParameter('p', 'bar', 'string');
            //We retrieve the result
            $result = $query->getResult();

            //Now we test the result
            static::assertCount(1, $result);
            static::assertEquals($pointO, $result[0][0]);
            static::assertSame('POLYGON((-4 -4,4 -4,4 4,-4 4,-4 -4))', $result[0][1]);
        }

.. _Common directory: https://github.com/Alexandre-T/doctrine2-spatial/tree/master/lib/CrEOF/Spatial/ORM/Query/AST/Functions/Common
.. _MySql directory: https://github.com/Alexandre-T/doctrine2-spatial/tree/master/lib/CrEOF/Spatial/ORM/Query/AST/Functions/MySql
.. _PostgreSql directory: https://github.com/Alexandre-T/doctrine2-spatial/tree/master/lib/CrEOF/Spatial/ORM/Query/AST/Functions/PostgreSql
.. _Standard directory: https://github.com/Alexandre-T/doctrine2-spatial/tree/master/lib/CrEOF/Spatial/ORM/Query/AST/Functions/Standard
.. _ISO/IEC 13249-3: https://www.iso.org/standard/60343.html
.. _OGC standards: https://www.ogc.org/standards/sfs
.. _ST_Polygonize: https://postgis.net/docs/manual-2.5/ST_Polygonize.html
.. _SnapToGrid: https://github.com/Alexandre-T/doctrine2-spatial/tree/master/lib/CrEOF/Spatial/ORM/Query/AST/Functions/PostgreSql/SpSnapToGrid.php
.. _functions test directory: https://github.com/Alexandre-T/doctrine2-spatial/tree/master/tests/CrEOF/Spatial/ORM/Query/AST/Functions/
