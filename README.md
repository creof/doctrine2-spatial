# doctrine2-spatial

Doctrine2 multi-platform support for spatial types and functions.

This package is a refactor/continuation of my [doctrine2-mysql-spatial](https://github.com/djlambert/doctrine2-mysql-spatial) package.

## Types
The following SQL types have been implemented as PHP objects and Doctrine types:

* Geometry
    * Point
    * LineString
    * Polygon

* Geography (PostgreSQL/PostGIS)
    * Point
    * LineString
    * Polygon

* Planned
    * MultiPoint
    * MultiLineString
    * MultiPolygon
    * GeometryCollection
    * 3D/4D geometries ??
    * Rasters ??????

There is support for both WKB/WKT and EWKB/EWKT return values. Currently only WKB/EWKB is used in statements.

## Functions
Currently the following SQL functions are supported in DQL (more coming):

### PostgreSQL
* ST_Area
* ST_AsBinary
* ST_AsText
* ST_Centroid
* ST_ClosestPoint
* ST_Contains
* ST_ContainsProperly
* ST_CoveredBy
* ST_Crosses
* ST_Disjoint
* ST_Envelope
* ST_GeomFromText
* ST_Length
* ST_LineCrossingDirection
* ST_StartPoint

### MySQL
* Area
* AsBinary
* AsText
* Contains
* Disjoint
* Envelope
* GeomFromText
* GLength
* MBRContains
* MBRDisjoint
* StartPoint

## DQL AST Walker
A DQL AST walker is included which when used with the following DQL functions will return the appropriate Geometry type object from queries instead of strings:

* AsText
* ST_AsText
* AsBinary
* ST_AsBinary

EWKT/EWKB function support planned.

### Example:
        $query = $this->em->createQuery('SELECT AsText(StartPoint(l.lineString)) MyLineStringEntity l');

        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();

```$result[n][1]``` will now be of type ```Point``` instead of the string ```'POINT(X Y)'```

