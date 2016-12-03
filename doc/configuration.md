# Configuration
Add the types and functions you need to your Symfony configuration file. The doctrine type names are not hardcoded.

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
			st_contains:     CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STContains
			contains:     CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Contains
			st_area:         CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Area
			st_geomfromtext: CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeomFromText
			st_intersects:     CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STIntersects
			st_buffer:     CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STBuffer
			point: CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Point
