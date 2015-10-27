# doctrine2-spatial

[![Code Climate](https://codeclimate.com/github/creof/doctrine2-spatial/badges/gpa.svg)](https://codeclimate.com/github/creof/doctrine2-spatial)
[![Test Coverage](https://codeclimate.com/github/creof/doctrine2-spatial/badges/coverage.svg)](https://codeclimate.com/github/creof/doctrine2-spatial/coverage)

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
 * STArea
 * STAsBinary
 * STAsGeoJson
 * STAsText
 * STAzimuth
 * STBoundary
 * STBuffer
 * STCentroid
 * STClosestPoint
 * STContains
 * STContainsProperly
 * STCoveredBy
 * STCovers
 * STCrosses
 * STDisjoint
 * STDistance
 * STDistanceSphere
 * STDWithin
 * STEnvelope
 * STExpand
 * STExtent
 * STGeomFromText
 * STIntersection
 * STIntersects
 * STLength
 * STLineCrossingDirection
 * STLineInterpolatePoint
 * STMakeBox2D
 * STMakeLine
 * STMakePoint
 * STPerimeter
 * STPoint
 * STScale
 * STSetSRID
 * STSimplify
 * STStartPoint
 * STSummary
 * STTouches
 * STTransform

### MySQL
 * Area
 * AsBinary
 * AsText
 * Buffer
 * Centroid
 * Contains
 * Crosses
 * Dimension
 * Distance
 * Disjoint
 * DistanceFromMultyLine
 * EndPoint
 * Envelope
 * Equals
 * ExteriorRing
 * GeodistPt
 * GeometryType
 * GeomFromText
 * GLength
 * InteriorRingN
 * Intersects
 * IsClosed
 * IsEmpty
 * IsSimple
 * LineStringFromWKB
 * LineString
 * MBRContains
 * MBRDisjoint
 * MBREqual
 * MBRIntersects
 * MBROverlaps
 * MBRTouches
 * MBRWithin
 * NumInteriorRings
 * NumPoints
 * Overlaps
 * PointFromWKB
 * PointN
 * Point
 * SRID
 * StartPoint
 * ST_Buffer
 * ST_Contains
 * ST_Crosses
 * ST_Disjoint
 * ST_Equals
 * ST_Intersects
 * ST_Overlaps
 * ST_Touches
 * ST_Within
 * Touches
 * Within
 * X
 * Y


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
```php
$query = $this->em->createQuery('SELECT AsText(StartPoint(l.lineString)) MyLineStringEntity l');

$query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');

$result = $query->getResult();
```
```$result[n][1]``` will now be of type ```Point``` instead of the string ```'POINT(X Y)'```

