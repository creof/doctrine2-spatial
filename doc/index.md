# Doctrine2-Spatial

The **Doctrine2-Spatial** library allows you to use spatial methods on your
MySQL and PostGIS database.

 * [Symfony Install](install.md)
 * [Symfony Configuration](configuration.md)
 * [Common Methods](common.md) (also included in MySQL and PostGreSQL)
 * [MySQL Methods](mysql.md)
 * [PostGreSQL Methods](postgresql.md)

## Types
The following SQL/OpenGIS types have been implemented as PHP objects and accompanying Doctrine types:

### Geometry
* Point
* LineString
* Polygon
* MultiPoint
* MultiLineString
* MultiPolygon

### Geography
Similar to Geometry but SRID value is always used (SRID supported in PostGIS only), and accepts only valid "geographic" coordinates.

* Point
* LineString
* Polygon

There is support for both WKB/WKT and EWKB/EWKT return values. Currently only WKT/EWKT is used in statements.

### DQL AST Walker
A DQL AST walker is included which when used with the following DQL functions will return the appropriate Geometry type object from queries instead of strings:

* AsText
* ST_AsText
* AsBinary
* ST_AsBinary

### Queries

Use method names in queries

```php
    $query = $this->em->createQuery('SELECT AsText(StartPoint(l.lineString)) MyLineStringEntity l');
    
    $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');
    
    $result = $query->getResult();
```

Or within an expression

```php
    $queryBuilder = $manager->createQueryBuilder();
    $queryBuilder
        ->select("id, ST_AsText(things.geometry) as geometry")
        ->from("geometryOfThings", "things")
        ->where(
            $queryBuilder->expr()->eq(
                sprintf("ST_Intersects(things.geometry, ST_SetSRID(ST_GeomFromGeoJSON('%s'), 4326))", $geoJsonPolygon),
                $queryBuilder->expr()->literal(true)
            )
        );
    $results = $queryBuilder->getQuery()->getResult();
```
