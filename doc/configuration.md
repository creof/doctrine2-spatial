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
                Geometry:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\Geometry
                ST_Buffer:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STBuffer
                ST_Collect:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCollect
                ST_SnapToGrid:            CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSnapToGrid
                ST_Overlaps:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STOverlaps
            numeric_functions:
                # for postgresql
                ST_Area:                  CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STArea
                ST_AsVinary:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsBinary
                ST_AsGeoJSON:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsGeoJson
                ST_AsText:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsText
                ST_Azimuth:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAzimuth
                ST_Boundary:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STBoundary
                ST_Centroid:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCentroid
                ST_ClosestPoint:          CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STClosestPoint
                ST_Contains:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContains
                ST_Containsproperly:      CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContainsProperly
                ST_CoveredBy:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCoveredBy
                ST_Covers:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCovers
                ST_Crosses:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STCrosses
                ST_Disjoint:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDisjoint
                ST_Distance:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDistance
                ST_Distancesphere:        CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDistanceSphere
                ST_DWithin:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDWithin
                ST_Envelope:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STEnvelope
                ST_Expand:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STExpand
                ST_Extent:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STExtent
                ST_GeomFromText:          CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STGeomFromText
                ST_Intersection:          CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STIntersection
                ST_Intersects:            CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STIntersects
                ST_Length:                CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLength
                ST_LineCrossingDirection: CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLineCrossingDirection
                ST_LineInterpolatePoint:  CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLineInterpolatePoint
                ST_MakeBox2D:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STMakeBox2D
                ST_MakeLine:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STMakeLine
                ST_MakePoint:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STMakePoint
                ST_Perimeter:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STPerimeter
                ST_Point:                 CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STPoint
                ST_Scale:                 CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STScale
                ST_SetSRID:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSetSRID
                ST_Simplify:              CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSimplify
                ST_StartPoint:            CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STStartPoint
                ST_Summary:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STSummary
                ST_Touches:               CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STTouches
                ST_Transform:             CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STTransform

                # for mysql
                # (deprecated 5.7.6)
                Area:                   CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Area
                AsBinary:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\AsBinary
                AsText:                 CrEOF\Spatial\ORM\Query\AST\Functions\MySql\AsText
                Buffer:                 CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Buffer
                Centroid:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Centroid
                Contains:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Contains
                Crosses:                CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Crosses
                Dimension:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Dimension
                Disjoint:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Disjoint
                Distance:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Distance
                Endpoint:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\EndPoint
                Envelope:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Envelope
                Equals:                 CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Equals
                ExteriorRing:           CrEOF\Spatial\ORM\Query\AST\Functions\MySql\ExteriorRing
                GeometryType:           CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeometryType
                GeomFromText:           CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeomFromText
                GLength:                CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GLength
                InteriorRingN:          CrEOF\Spatial\ORM\Query\AST\Functions\MySql\InteriorRingN
                Intersects:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Intersects
                IsClosed:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\IsClosed
                IsEmpty:                CrEOF\Spatial\ORM\Query\AST\Functions\MySql\IsEmpty
                IsSimple:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\IsSimple
                LineStringFromWKB:      CrEOF\Spatial\ORM\Query\AST\Functions\MySql\LineStringFromWKB
                NumInteriorRings:       CrEOF\Spatial\ORM\Query\AST\Functions\MySql\NumInteriorRings
                NumPoints:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\NumPoints
                Overlaps:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Overlaps
                PointFromWKB:           CrEOF\Spatial\ORM\Query\AST\Functions\MySql\PointFromWKB
                PointN:                 CrEOF\Spatial\ORM\Query\AST\Functions\MySql\PointN
                SRID:                   CrEOF\Spatial\ORM\Query\AST\Functions\MySql\SRID
                Startpoint:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\StartPoint
                Touches:                CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Touches
                Within:                 CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Within
                X:                      CrEOF\Spatial\ORM\Query\AST\Functions\MySql\X
                Y:                      CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Y
		# (current)
                LineString:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\LineString
                MBRContains:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRContains
                MBRDisjoint:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRDisjoint
                MBREqual:               CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBREqual
                MBRIntersects:          CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRIntersects
                MBROverlaps:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBROverlaps
                MBRTouches:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRTouches
                MBRWithin:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\MBRWithin
                Point:                  CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Point
                ST_Buffer:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STBuffer
                ST_Contains:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STContains
                ST_Crosses:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STCrosses
                ST_Disjoint:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STDisjoint
                ST_Equals:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STEquals
                ST_Intersects:          CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STIntersects
                ST_Overlaps:            CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STOverlaps
                ST_Touches:             CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STTouches
                ST_Within:              CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STWithin

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

