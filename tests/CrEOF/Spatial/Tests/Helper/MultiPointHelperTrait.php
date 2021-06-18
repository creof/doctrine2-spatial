<?php
/**
 * Copyright (C) 2020 Alexandre Tranchant
 * Copyright (C) 2015 Derek J. Lambert
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace CrEOF\Spatial\Tests\Helper;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\PHP\Types\Geometry\MultiPoint;
use CrEOF\Spatial\PHP\Types\Geometry\Point as GeometryPoint;
use CrEOF\Spatial\Tests\Fixtures\MultiPointEntity;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

/**
 * MultipointPointHelperTrait Trait.
 *
 * This helper provides some methods to generates multipoint entities.
 * All of these points are defined in test documentation.
 *
 * Methods beginning with create will store a geo* entity in database.
 *
 * @see /doc/test.md
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @method EntityManagerInterface getEntityManager Return the entity interface
 */
trait MultiPointHelperTrait
{
    /**
     * Create A Multipoint entity entity composed of four points and store it in database.
     *
     * @throws InvalidValueException        when geographies are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createFourPoints(): MultiPointEntity
    {
        $multipoint = new MultiPoint([]);
        $multipoint->addPoint(new GeometryPoint(0, 0));
        $multipoint->addPoint(new GeometryPoint(0, 1));
        $multipoint->addPoint(new GeometryPoint(1, 0));
        $multipoint->addPoint(new GeometryPoint(1, 1));

        return $this->createMultipoint($multipoint);
    }

    /**
     * Create A Multipoint entity entity composed of one point and store it in database.
     *
     * @throws InvalidValueException        when geographies are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createSinglePoint(): MultiPointEntity
    {
        $multipoint = new MultiPoint([]);
        $multipoint->addPoint(new GeometryPoint(0, 0));

        return $this->createMultipoint($multipoint);
    }

    /**
     * Create a geometric MultiPoint entity from an array of geometric points.
     *
     * @param MultiPoint $multipoint Each point could be an array of X, Y or an instance of Point class
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    private function createMultipoint(MultiPoint $multipoint): MultiPointEntity
    {
        $multiPointEntity = new MultiPointEntity();
        $multiPointEntity->setMultiPoint($multipoint);
        $this->getEntityManager()->persist($multiPointEntity);

        return $multiPointEntity;
    }
}
