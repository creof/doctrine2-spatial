<?php

namespace CrEOF\Spatial\Tests\ORM\Query\AST\Functions\PostgreSql;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\PointEntity;
use CrEOF\Spatial\Tests\OrmTestCase;

/**
 * ST_SnapToGrid DQL function tests
 *
 * @author  Dragos Protung
 * @license http://dlambert.mit-license.org MIT
 *
 * @group dql
 */
class STSnapToGridTest extends OrmTestCase
{
    protected function setUp()
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * @group geometry
     */
    public function testSelectSTSnapToGridSignature2Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(1.25, 2.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery('SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), 0.5)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p');
        $result = $query->getResult();

        $expected = array(
            array(1 => 'POINT(1 2.5)'),
        );

        $this->assertEquals($expected, $result);
    }

    /**
     * @group geometry
     */
    public function testSelectSTSnapToGridSignature3Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(1.25, 2.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery('SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), 0.5, 1)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p');
        $result = $query->getResult();

        $expected = array(
            array(1 => 'POINT(1 3)'),
        );

        $this->assertEquals($expected, $result);
    }

    /**
     * @group geometry
     */
    public function testSelectSTSnapToGridSignature5Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(5.25, 6.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery('SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), 5.55, 6.25, 0.5, 0.5)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p');
        $result = $query->getResult();

        $expected = array(
            array(1 => 'POINT(5.05 6.75)'),
        );

        $this->assertEquals($expected, $result);
    }

    /**
     * @group geometry
     */
    public function testSelectSTSnapToGridSignature6Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(5.25, 6.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery('SELECT ST_AsText(ST_SnapToGrid(geometry(p.point), geometry(p.point), 0.005, 0.025, 0.5, 0.01)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p');
        $result = $query->getResult();

        $expected = array(
            array(1 => 'POINT(5.25 6.55)'),
        );

        $this->assertEquals($expected, $result);
    }
}
