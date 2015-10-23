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
            numeric_functions:
            # for postgresql
                st_contains:     CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContains
                st_distance:     CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDistance
                st_area:         CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STArea
                st_length:       CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLength
                st_geomfromtext: CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STGeomFromText

                # for mysql
                area:               CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Area
                as_binary:          CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\AsBinary
                as_text:            CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\AsText
                centroid:           CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Centroid
                contains:           CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Contains
                crosses:            CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Crosses
                dimension:          CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Dimension
                disjoint:           CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Disjoint
                end_point:          CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\EndPoint
                envelope:           CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Envelope
                equals:             CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Equals
                exterior_ring:      CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\ExteriorRing
                geometry_type:      CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\GeometryType
                geom_from_text:     CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\GeomFromText
                glength:            CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\GLength
                interior_ring_n:    CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\InteriorRingN
                intersects:         CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Intersects
                is_closed:          CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\IsClosed
                is_empty:           CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\IsEmpty
                is_simple:          CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\IsSimple
                mbr_contains:       CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\MBRContains
                mbr_disjoint:       CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\MBRDisjoint
                mbr_intersects:     CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\MBRIntersects
                mbr_overlaps:       CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\MBROverlaps
                mbr_touches:        CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\MBRTouches
                mbr_within:         CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\MBRWithin
                num_interior_rings: CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\NumInteriorRings
                num_points:         CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\NumPoints
                overlaps:           CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Overlaps
                point_n:            CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\PointN
                srid:               CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\SRID
                start_point:        CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\StartPoint
                touches:            CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Touches
                within:             CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Within
                x:                  CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\X
                y:                  CrEOF\Spatial\ORM\Query\AST\Functions\MySQL\Y
    ```
    
