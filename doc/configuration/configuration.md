# Configuration
Add the types and functions you need to your Symfony doctrine configuration file. 
The doctrine type names are not hardcoded. To be compliant with OGC, prefix functions with ST_ . So,
If you deploy your code on another database server, you will not have to change your code :)

```yaml
doctrine:
    dbal:
        types:
            geometry:   CrEOF\Spatial\DBAL\Types\GeometryType
            point:      CrEOF\Spatial\DBAL\Types\Geometry\PointType
            polygon:    CrEOF\Spatial\DBAL\Types\Geometry\PolygonType
            linestring: CrEOF\Spatial\DBAL\Types\Geometry\LineStringType

    orm:
        dql:
            numeric_functions:
                #Declare functions returning a numeric
                #To be compliant with OGC, prefix functions with ST_
                st_contains: CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STContains
                #deprecated, but if you use an old version of MySQL  
                #st_contains: CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Contains
            string_functions:
                #Declare functions returning a numericstring
                st_envelope: CrEOF\Spatial\ORM\Query\AST\Functions\PostGreSQL\STEnvelope
                #You can use the DQL function name the you want and then use it in your DQL
                myDQLFunctionName: CrEOF\Spatial\ORM\Query\AST\Functions\PostGreSQL\STCentroid
                #SELECT myDQLFunctionName(POLYGON(...
```

Add the functions you want to use in these three lists:
1. [list of functions used by PostGreSQL and MySQL](../common.md)
2. [list of functions used by PostGreSQL](../postgresql.md)
3. [list of functions used by MySQL](../mysql.md)

Be warned that [MySQL spatial functions have a lot of bugs](https://sqlpro.developpez.com/tutoriel/dangers-mysql-mariadb/),
does not return the same results than MariaDB (which is bugged too). If you want to store geometric or geographic data, 
please considers to use PostgreSQL Server or Microsoft SQL Server.

Nota: By default, function declared by the [Open Geospatial Consortium](https://www.ogc.org/) in the 
[OGC Standards: SQL Option](https://www.ogc.org/standards/sfs) are prefixed by ST_, other functions should