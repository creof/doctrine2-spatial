# Symfony2 Install

## composer.json
    "require": {
    	...
        "slavenin/doctrine2-spatial": "dev-master"

You will also have to change the version requirement of doctrine to at least 2.1:

        "doctrine/orm": ">=2.1",


## config.yml
Add the types and functions you need to your Symfony configuration. The doctrine type names are not hardcoded.

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
	                numeric_functions:
					st_contains:     CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STContains
					contains:     CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Contains
					st_area:         CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Area
					st_geomfromtext: CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeomFromText
					st_intersects:     CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STIntersects
                	st_buffer:     CrEOF\Spatial\ORM\Query\AST\Functions\MySql\STBuffer
					point: CrEOF\Spatial\ORM\Query\AST\Functions\MySql\Point
					geodist_pt: CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeodistPt
                	distance_from_multyline: CrEOF\Spatial\ORM\Query\AST\Functions\MySql\DistanceFromMultyLine

## Add new function in dql
Simply add new class in vendor\slavenin\doctrine2-spatial\lib\CrEOF\Spatial\ORM\Query\AST\Functions\MySql\ with need function name and config.yml.

Example:

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