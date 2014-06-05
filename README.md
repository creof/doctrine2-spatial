# doctrine2-spatial

Doctrine2 multi-platform support for spatial types and functions. Currently MySQL and PostgreSQL with PostGIS are supported. Could potentially add support for other platforms if an interest is expressed.

This package is a refactor/continuation of my [doctrine2-mysql-spatial](https://github.com/djlambert/doctrine2-mysql-spatial) package.

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

### Planned

* GeometryCollection
* 3D/4D geometries ??
* Rasters ??????

There is support for both WKB/WKT and EWKB/EWKT return values. Currently only WKT/EWKT is used in statements.

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
* ST_Covers
* ST_Crosses
* ST_Disjoint
* ST_Distance
* ST_Envelope
* ST_GeomFromText
* ST_Length
* ST_LineCrossingDirection
* ST_StartPoint
* ST_Summary

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

## Setup/Installation

If you're using Doctrine with Symfony2 take a look at INSTALL.md, otherwise the OrmTest.php test class shows their use with Doctrine alone.

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

