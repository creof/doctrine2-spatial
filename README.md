# doctrine2-spatial

Doctrine2 multi-platform support for spatial types and functions.

This project is a refactor/continuation of my [doctrine2-mysql-spatial](https://github.com/djlambert/doctrine2-mysql-spatial) pacakge.

## Types
* Geometry
* Point
* LineString
* Polygon
* (todo) MultiPoint
* (todo) MultiLineString
* (todo) MultiPolygon
* (todo) GeometryCollection

## Functions
Currently the following SQL functions are supported in DQL and more coming:

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
There is a DQL AST walker that in conjunction with the AsText/ST_AsText or AsBinary/ST_AsBinary DQL functions will return the appropriate Geometry type object from queries instead of strings.

        $query = $this->em->createQuery('SELECT AsText(StartPoint(l.lineString)) MyLineStringEntity l');

        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'CrEOF\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();

```$result[n][1]``` will now be of type ```Point``` instead of the string ```'POINT(X Y)'```

