# PostgreSQL

## Functions

* ST_Area
* ST_AsBinary
* ST_AsGeoJson
* ST_AsText
* ST_Azimuth
* ST_Boundary
* ST_Buffer
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
* ST_Expand
* ST_Extent
* ST_GeomFromGeoJSON
* ST_GeomFromText
* ST_Intersection
* ST_Intersects
* ST_Length
* ST_LineCrossingDirection
* ST_LineInterpolatePoint
* ST_MakeBox2D
* ST_MakeEnvelope
* ST_MakeLine
* ST_Point
* ST_Scale
* ST_SetSRID
* ST_Simplify
* ST_StartPoint
* ST_Summary
* ST_Touches
* ST_Transform
* ST_Perimeter

## New
* GeometryType(geometry)
* ST_Area(geometry)
* ST_AsBinary(geometry)
* ST_AsText(geometry)
* ST_BdMPolyFromText(text WKT, integer SRID)
* ST_BdPolyFromText(text WKT, integer SRID)
* ST_Boundary(geometry)
* ST_Buffer(geometry, double, [integer])
* ST_Centroid(geometry)
* ST_Contains(geometry A, geometry B)
* ST_ConvexHull(geometry)
* ST_CoveredBy(geometry A, geometry B)
* ST_Covers(geometry A, geometry B)
* ST_Crosses(geometry, geometry)
* ST_Difference(geometry A, geometry B)
* ST_Dimension(geometry)
* ST_Disjoint(geometry, geometry)
* ST_Distance(geometry, geometry)
* ST_DWithin(geometry, geometry, float)
* ST_EndPoint(geometry)
* ST_Envelope(geometry)
* ST_Equals(geometry, geometry)
* ST_ExteriorRing(geometry)
* ST_GeoHash()
* ST_GeomCollFromText()
* ST_GeomCollFromText(text,[<srid>])
* ST_GeomCollFromWKB(bytea,[<srid>])
* ST_GeometryFromWKB(bytea,[<srid>])
* ST_GeometryN(geometry,int)
* ST_GeomFromGeoJSON()
* ST_GeomFromKML(text geomkml)
* ST_GeomFromText(text,[<srid>])
* ST_GeomFromWKB(bytea,[<srid>])
* ST_InteriorRingN(geometry,integer)
* ST_Intersection(geometry, geometry)
* ST_Intersects(geometry, geometry)
* ST_IsClosed(geometry)
* ST_IsCollection
* ST_IsEmpty(geometry)
* ST_IsRing(geometry)
* ST_IsSimple(geometry)
* ST_IsValid()
* ST_Length(geometry)
* ST_LineFromText(text,[<srid>])
* ST_LineFromWKB(bytea,[<srid>])
* ST_LinestringFromWKB(bytea,[<srid>])
* ST_M(geometry)
* ST_MakeEnvelope()
* ST_MemUnion(geometry set)
* ST_MLineFromText(text,[<srid>])
* ST_MLineFromWKB(bytea,[<srid>])
* ST_MPointFromText(text,[<srid>])
* ST_MPointFromWKB(bytea,[<srid>])
* ST_MPolyFromText(text,[<srid>])
* ST_MPolyFromWKB(bytea,[<srid>])
* ST_NumGeometries(geometry)
* ST_NumInteriorRing(geometry)
* ST_NumPoints(geometry)
* ST_Overlaps(geometry, geometry)
* ST_Point()
* ST_PointFromGeoHash()
* ST_PointFromText(text,[<srid>])
* ST_PointFromWKB(bytea,[<srid>])
* ST_PointN(geometry,integer)
* ST_PointOnSurface(geometry)
* ST_PolyFromText(text,[<srid>])
* ST_PolyFromWKB(bytea,[<srid>])
* ST_PolygonFromText(text,[<srid>])
* ST_PolygonFromWKB(bytea,[<srid>])
* ST_Relate(geometry, geometry, intersectionPatternMatrix)
* ST_Relate(geometry, geometry)
* ST_Shift_Longitude(geometry)
* ST_SimplifyPreserveTopology(geometry, double)
* ST_Split
* ST_SRID(geometry)
* ST_StartPoint(geometry)
* ST_SymDifference(geometry A, geometry B)
* ST_Touches(geometry, geometry)
* ST_UnaryUnion
* ST_Union(geometry set)
* ST_Union(geometry, geometry)
* ST_Within(geometry A, geometry B)
* ST_X(geometry)
* ST_Y(geometry)
* ST_Z(geometry)
