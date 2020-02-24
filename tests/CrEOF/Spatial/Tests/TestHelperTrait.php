<?php

namespace CrEOF\Spatial\Tests;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\Tests\Fixtures\PolygonEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * TestHelperTrait Trait.
 *
 * This helper provides some methods to generates polygons, linestring and point.
 * All of these geometries are defined in test documentation
 *
 * @see /doc/test.md
 *
 * @method EntityManagerInterface getEntityManager Return the entity interface
 */
trait TestHelperTrait
{
    /**
     * Return an external linestring.
     *
     * @throws InvalidValueException this cannot happen
     *
     * @return LineString
     */
    protected static function createEnvelopingLineString()
    {
        return new LineString([
            new Point(0, 0),
            new Point(10, 0),
            new Point(10, 10),
            new Point(0, 10),
            new Point(0, 0),
        ]);
    }

    /**
     * Return an internal linestring.
     *
     * @throws InvalidValueException this cannot happen
     *
     * @return LineString
     */
    protected static function createInternalLineString()
    {
        return new LineString([
            new Point(5, 5),
            new Point(7, 5),
            new Point(7, 7),
            new Point(5, 7),
            new Point(5, 5),
        ]);
    }

    /**
     * Return a linestring out of the enveloping linestring.
     *
     * @throws InvalidValueException this cannot happen
     *
     * @return LineString
     */
    protected static function createOuterLineString()
    {
        return new LineString([
            new Point(15, 15),
            new Point(17, 15),
            new Point(17, 17),
            new Point(15, 17),
            new Point(15, 15),
        ]);
    }

    /**
     * Create the BIG Polygon and persist it in database.
     *
     * @return PolygonEntity
     *
     * @throws InvalidValueException when geometries are not valid
     */
    protected function createBigPolygon()
    {
        return $this->createPolygon([self::createEnvelopingLineString()]);
    }

    /**
     * Create the HOLEY Polygon and persist it in database.
     * (Big polygon minus Small Polygon)
     *
     * @return PolygonEntity
     *
     * @throws InvalidValueException when geometries are not valid
     */
    protected function createHoleyPolygon()
    {
        return $this->createPolygon([self::createEnvelopingLineString(),self::createInternalLineString()]);
    }

    /**
     * Create the W Polygon and persist it in database.
     *
     * @return PolygonEntity
     *
     * @throws InvalidValueException when geometries are not valid
     */
    protected function createPolygonW()
    {
        return $this->createPolygon([
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 20),
                new Point(0, 20),
                new Point(10, 10),
                new Point(0, 0),
            ])
        ]);
    }

    /**
     * Create the SMALL Polygon and persist it in database.
     *
     * @return PolygonEntity
     *
     * @throws InvalidValueException when geometries are not valid
     */
    protected function createSmallPolygon()
    {
        return $this->createPolygon([self::createInternalLineString()]);
    }

    /**
     * Create a Polygon from an array of linestrings.
     *
     * @param array $lineStrings the array of linestrings
     *
     * @return PolygonEntity
     *
     * @throws InvalidValueException when geometries are not valid
     */
    protected function createPolygon(array $lineStrings)
    {
        $polygon = new PolygonEntity();
        $polygon->setPolygon(new Polygon($lineStrings));
        $this->getEntityManager()->persist($polygon);

        return $polygon;
    }

}