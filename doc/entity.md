# How to create an entity with spatial data?

It is a good practice to use the most adapted column to store you geometric or geographic data. 
If your entity have only to store points, do not use a "geometry" type, but a "point" type.
Use a geometry column only if your entity can store different types (points and lines as example) 

Here is an example to declare an entity with a point :
```php
<?php

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\Mapping as ORM;

/**
 * Point entity example.
 *
 * @ORM\Entity
 * @ORM\Table
 */
class PointEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var Point
     *
     * As you can see we declare a point of type point.
     * point shall be declared in the doctrine.yaml as a custom type
     * 
     * @ORM\Column(type="point", nullable=true)
     */
    protected $point;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get point.
     * This is a standard getter.
     * @return Point
     */
    public function getPoint(): Point
    {
        return $this->point;
    }

    /**
     * Set point.
     * This is a fluent setter. 
     *
     * @param Point $point point to set
     *
     * @return self
     */
    public function setPoint(Point $point): self
    {
        $this->point = $point;

        return $this;
    }
}

```
In the [Fixtures directory](../tests/CrEOF/Spatial/Tests/Fixtures) used for test, you will find a lot of entities which are 
implementing geometric or geographic columns:
* Entity with a *[geography](../tests/CrEOF/Spatial/Tests/Fixtures/GeographyEntity.php)* type
* Entity with a *[geographic linestring](../tests/CrEOF/Spatial/Tests/Fixtures/GeoLineStringEntity.php)* type
* Entity with a *[geographic point](../tests/CrEOF/Spatial/Tests/Fixtures/GeoPointSridEntity.php)* type
* Entity with a *[geographic polygon](../tests/CrEOF/Spatial/Tests/Fixtures/GeoPolygonEntity.php)* type
* Entity with a *[geometry](../tests/CrEOF/Spatial/Tests/Fixtures/NoHintGeometryEntity.php)* type
* Entity with a *[geometric linestring](../tests/CrEOF/Spatial/Tests/Fixtures/GeoLineStringEntity.php)* type
* Entity with a *[geometric multilinestring](../tests/CrEOF/Spatial/Tests/Fixtures/MultiLineStringEntity.php)* type
* Entity with a *[geometric multipoint](../tests/CrEOF/Spatial/Tests/Fixtures/MultiPointEntity.php)* type
* Entity with a *[geometric multipolygon](../tests/CrEOF/Spatial/Tests/Fixtures/MultiPolygonEntity.php)* type
* Entity with a *[geometric point](../tests/CrEOF/Spatial/Tests/Fixtures/PointEntity.php)* type
* Entity with a *[geometric polygon](../tests/CrEOF/Spatial/Tests/Fixtures/PolygonEntity.php)* type
