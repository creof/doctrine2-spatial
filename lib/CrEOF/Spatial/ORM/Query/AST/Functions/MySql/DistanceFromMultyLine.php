<?php

namespace CrEOF\Spatial\ORM\Query\AST\Functions\MySql;

use CrEOF\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;

/**
 * Description of STContains
 *
 * @author Maximilian
 */
class DistanceFromMultyLine extends AbstractSpatialDQLFunction
{
	protected $platforms = array('mysql');

    protected $functionName = 'distance_from_multyline';

    protected $minGeomExpr = 2;

    protected $maxGeomExpr = 2;
}
