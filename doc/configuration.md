## Configuration
Add the types and functions you need to your Symfony doctrine configuration file. 
The doctrine type names are not hardcoded. To be compliant with OGC, prefix functions with ST_ . So,
if you deploy your code on another database server, you will not have to change your code :)

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
                #Declare functions returning a numeric value
                #A good practice is to prefix functions with ST_ when they are issue from the Standard directory
                st_contains: CrEOF\Spatial\ORM\Query\AST\Functions\Standard\STContains
            string_functions:
                #Declare functions returning a string
                st_envelope: CrEOF\Spatial\ORM\Query\AST\Functions\Standard\STEnvelope
                #A good practice is to prefix functions with SP_ when they are not issue from the Standard directory
                sp_asgeojson: CrEOF\Spatial\ORM\Query\AST\Functions\Postgresql\SpAsGeoJson
                #You can use the DQL function name you want and then use it in your DQL
                myDQLFunctionAlias: CrEOF\Spatial\ORM\Query\AST\Functions\Standard\StCentroid
                #SELECT myDQLFunctionAlias(POLYGON(...
```

Add the functions you want to use in these three lists:
1. [list of functions declared in the OGC Standard](./standard/index.md)
2. [list of PostGreSQL functions which are not declared in the OGC Standard](./postgresql/index.md)
3. [list of MySQL functions which are not declared in the OGC Standard](./mysql/index.md)

Be warned that [MySQL spatial functions have a lot of bugs](https://sqlpro.developpez.com/tutoriel/dangers-mysql-mariadb/),
they does not return the same results than MariaDB (which is bugged too). If you want to store geometric or geographic data, 
please considers to use a good database server such as PostgreSQL Server or Microsoft SQL Server.

Nota: By default, function declared by the [Open Geospatial Consortium](https://www.ogc.org/) in the 
[OGC Standards: SQL Option](https://www.ogc.org/standards/sfs) are prefixed by ST_, other functions should not be declared with this prefix

