Configuration
=============

Configuration for applications using Symfony framework
------------------------------------------------------
To configure Doctrine spatial extension on your Symfony application, you only need to edit your ``config/doctrine.yaml``
file. Two steps are sufficient. First step will help you to declare spatial types on DQL. The second step will help you
to declare a spatial function.

Declare your geometric types
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: yaml

    doctrine:
        dbal:
            types:
                geometry:   CrEOF\Spatial\DBAL\Types\GeometryType
                point:      CrEOF\Spatial\DBAL\Types\Geometry\PointType
                polygon:    CrEOF\Spatial\DBAL\Types\Geometry\PolygonType
                linestring: CrEOF\Spatial\DBAL\Types\Geometry\LineStringType

Now, you can :doc:`create an entity <./Entity>` with a ``geometry``, ``point``, ``polygon`` and a ``linestring`` type.

Here is a complete example of all available types. The names of doctrine types are not hardcoded. So if you only want to
use the geometric type, feel free to remove the ``geometric_`` prefixes

.. code-block:: yaml

    doctrine:
        dbal:
            types:
                geography:            CrEOF\Spatial\DBAL\Types\GeographyType
                geography_linestring: CrEOF\Spatial\DBAL\Types\Geography\LineStringType
                geography_point:      CrEOF\Spatial\DBAL\Types\Geography\PointType
                geography_polygon:    CrEOF\Spatial\DBAL\Types\Geography\PolygonType

                geometry:            CrEOF\Spatial\DBAL\Types\GeometryType
                geometry_linestring: CrEOF\Spatial\DBAL\Types\Geometry\LineStringType
                geometry_point:      CrEOF\Spatial\DBAL\Types\Geometry\PointType
                geometry_polygon:    CrEOF\Spatial\DBAL\Types\Geometry\PolygonType
                geometry_multilinestring: CrEOF\Spatial\DBAL\Types\Geometry\MultiLineStringType
                geometry_multipoint:      CrEOF\Spatial\DBAL\Types\Geometry\MultiPointType
                geometry_multipolygon:    CrEOF\Spatial\DBAL\Types\Geometry\MultiPolygonType

I try to maintain this documentation up-to-date. In any case, the `DBAL/Types`_ directory contains all geometric and all
geographic available types.

Any help is welcomed to implement the other spatial types declared in the `Open Geospatial Consortium standard`_ and in
the `ISO/IEC 13249-3:2016`_ like ``Curve`` or ``PolyhedSurface``.

Declare a new function
^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: yaml

    orm:
        dql:
            numeric_functions:
                #Declare functions returning a numeric value
                #A good practice is to prefix functions with ST when they are issue from the Standard directory
                st_area: CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StArea
            string_functions:
                #Declare functions returning a string
                st_envelope: CrEOF\Spatial\ORM\Query\AST\Functions\Standard\STEnvelope
                #A good practice is to prefix functions with SP when they are not issue from the Standard directory
                sp_asgeojson: CrEOF\Spatial\ORM\Query\AST\Functions\Postgresql\SpAsGeoJson
                #You can use the DQL function name you want and then use it in your DQL
                myDQLFunctionAlias: CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StCentroid
                #SELECT myDQLFunctionAlias(POLYGON(...

Add only the functions you want to use. The list of available function can be found in these sections:

1. list of :ref:`Standard functions` declared in the `Open Geospatial Consortium standard`_,
2. list of :ref:`Specific PostGreSQL functions` which are not already declared in the OGC Standard,
3. list of :ref:`Specific MySQL functions` which are not already declared in the OGC Standard,

Be warned that `MySQL spatial functions have a lot of bugs`_, especially the ```Contains``` function which returns wrong
results. If you want to store geometric data, please considers to use a good database server such as PostgreSQL Server
or Microsoft SQL Server. If you want to store geographic data, you have to use PostgreSql server, because MySql
does not implements geographic data.

Nota: By default, function declared by the `Open Geospatial Consortium`_ in the `standards of SQL Options`_ are prefixed
by ``ST_``, other functions should not be declared with this prefix. We suggest to use the ``SP_`` prefix (specific).

Configuration for other application
-----------------------------------

Declare your geometric types
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Doctrine allows you to create new mapping types. We used this functionnality to create spatial types in this extension.
You only need to let Doctrine know which type you want to use. Two lines are sufficient to do it. The first line calls
the ``Type`` class. The second line, declare a type. In the below example, we declare a geometric ``point`` type.

.. code-block:: php

    <?php
    // in your bootstrapping code

    // ...

    use Doctrine\DBAL\Types\Type;

    // ...

    // Register types provided by the doctrine2 spatial extension
    Type::addType('point', 'CrEOF\Spatial\DBAL\Types\Geometry\PointType');

Declare a new function
^^^^^^^^^^^^^^^^^^^^^^

You can register functions of the doctrine spatial extension adding them to the ORM configuration:

.. code-block:: php

    <?php

    // in your bootstrapping code

    // ...

    use Doctrine\ORM\Configuration\Doctrine\ORM\Configuration;

    // ...

    $config = new Configuration();
    // This is an example to declare a standard spatial function which is returning a string
    $config->addCustomStringFunction('ST_Envelope', 'CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StEnvelope');
    // This is another example to declare a standard spatial function which is returning a numeric
    $config->addCustomNumericFunction('ST_Area', 'CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StArea');
    // This is another example to declare a Postgresql specific function which is returning a string
    $config->addCustomNumericFunction('SP_GeoJson', 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\SpGeoJson');


.. _ISO/IEC 13249-3:2016: https://www.iso.org/standard/60343.html
.. _MySQL spatial functions have a lot of bugs: https://sqlpro.developpez.com/tutoriel/dangers-mysql-mariadb/
.. _Open Geospatial Consortium: https://www.ogc.org/
.. _Open Geospatial Consortium standard: https://www.ogc.org/standards/sfs
.. _standards of SQL Options: https://www.ogc.org/standards/sfs
.. _DBAL/Types: https://github.com/creof/doctrine2-spatial/tree/master/lib/CrEOF/Spatial/DBAL/Types
