# Symfony2 Install

## composer.json
    "repositories": [
        { "type": "vcs", "url": "https://github.com/tvogt/doctrine2-spatial" }
    ],
    "require": {
    	...
        "creof/doctrine2-spatial": "dev-master"

You will also have to change the version requirement of doctrine:

        "doctrine/orm": "dev-master",

These two changes have been made in various forks and versions, so check first.



## config.yml
You need to manually add the types and functions you use:

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
	                st_contains:        CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STContains
	                st_distance:        CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STDistance
	                st_area:            CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STArea
	                st_length:          CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STLength
	                st_geomfromtext:    CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STGeomFromText
