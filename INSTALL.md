# Symfony2 Install

## composer.json
    "require": {
        ...
        "creof/doctrine2-spatial": "dev-master"

You will also have to change the version requirement of doctrine to at least 2.1:

        "doctrine/orm": ">=2.1",


## config.yml
Add the types and functions you need to your Symfony configuration. The doctrine type names are not hardcoded.

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
            string_functions:
                # for postgresql
                geometry:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\Geometry
                stbuffer:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STBuffer
                stcollect:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCollect
                stsnaptogrid:            CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSnapToGrid
                stoverlaps:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STOverlaps
            numeric_functions:
                # for postgresql
                starea:                  CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STArea
                stasbinary:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsBinary
                stasgeojson:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsGeoJson
                stastext:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsText
                stazimuth:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAzimuth
                stboundary:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STBoundary
                stcentroid:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCentroid
                stclosestpoint:          CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STClosestPoint
                stcontains:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContains
                stcontainsproperly:      CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContainsProperly
                stcoveredby:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCoveredBy
                stcovers:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCovers
                stcrosses:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCrosses
                stdisjoint:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDisjoint
                stdistance:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDistance
                stdistancesphere:        CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDistanceSphere
                stdwithin:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDWithin
                stenvelope:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STEnvelope
                stexpand:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STExpand
                stextent:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STExtent
                stgeomfromtext:          CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STGeomFromText
                stintersection:          CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STIntersection
                stintersects:            CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STIntersects
                stlength:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLength
                stlinecrossingdirection: CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLineCrossingDirection
                stlineinterpolatepoint:  CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLineInterpolatePoint
                stmakebox2d:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STMakeBox2D
                stmakeline:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STMakeLine
                stmakepoint:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STMakePoint
                stperimeter:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STPerimeter
                stpoint:                 CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STPoint
                stscale:                 CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STScale
                stsetsrid:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSetSRID
                stsimplify:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSimplify
                ststartpoint:            CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STStartPoint
                stsummary:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSummary
                sttouches:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STTouches
                sttransform:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STTransform

                # for mysql
                area:                   CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Area
                asbinary:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\AsBinary
                astext:                 CrEOF\Spatial\ORM\Query\AST\Functions\MySql\AsText
                buffer:                 CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Buffer
                centroid:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Centroid
                contains:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Contains
                crosses:                CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Crosses
                dimension:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Dimension
                distance:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Distance
                disjoint:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Disjoint
                distancefrommultyLine:  CrEOF\Spatial\ORM\Query\AST\Functions\MySql\DistanceFromMultyLine
                endpoint:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\EndPoint
                envelope:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Envelope
                equals:                 CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Equals
                exteriorring:           CrEOF\Spatial\ORM\Query\AST\Functions\MySql\ExteriorRing
                geodistpt:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeodistPt
                geometrytype:           CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeometryType
                geomfromtext:           CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeomFromText
                glength:                CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GLength
                interiorringn:          CrEOF\Spatial\ORM\Query\AST\Functions\MySql\InteriorRingN
                intersects:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Intersects
                isclosed:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\IsClosed
                isempty:                CrEOF\Spatial\ORM\Query\AST\Functions\MySql\IsEmpty
                issimple:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\IsSimple
                linestringfromwkb:      CrEOF\Spatial\ORM\Query\AST\Functions\MySql\LineStringFromWKB
                linestring:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\LineString
                mbrcontains:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRContains
                mbrdisjoint:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRDisjoint
                mbrequal:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBREqual
                mbrintersects:          CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRIntersects
                mbroverlaps:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBROverlaps
                mbrtouches:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRTouches
                mbrwithin:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRWithin
                numinteriorrings:       CrEOF\Spatial\ORM\Query\AST\Functions\MySql\NumInteriorRings
                numpoints:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\NumPoints
                overlaps:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Overlaps
                pointfromwkb:           CrEOF\Spatial\ORM\Query\AST\Functions\MySql\PointFromWKB
                pointn:                 CrEOF\Spatial\ORM\Query\AST\Functions\MySql\PointN
                point:                  CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Point
                srid:                   CrEOF\Spatial\ORM\Query\AST\Functions\MySql\SRID
                startpoint:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\StartPoint
                st_buffer:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STBuffer
                st_contains:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STContains
                st_crosses:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STCrosses
                st_disjoint:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STDisjoint
                st_equals:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STEquals
                st_intersects:          CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STIntersects
                st_overlaps:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STOverlaps
                st_touches:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STTouches
                st_within:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STWithin
                touches:                CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Touches
                within:                 CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Within
                x:                      CrEOF\Spatial\ORM\Query\AST\Functions\MySql\X
                y:                      CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Y

```

## Add new function in dql
Simply add new class in vendor\slavenin\doctrine2-spatial\lib\CrEOF\Spatial\ORM\Query\AST\Functions\MySql\ with need function name and config.yml.


Example:
```php
class Point extends AbstractSpatialDQLFunction
{
	protected $platforms = array('mysql');
	/*function name in dql*/
	protected $functionName = 'Point';
	/*min params count*/
    protected $minGeomExpr = 2;
	/*max params  count*/
	protected $maxGeomExpr = 2;
}
```

