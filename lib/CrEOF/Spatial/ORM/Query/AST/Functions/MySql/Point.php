<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CrEOF\Spatial\ORM\Query\AST\Functions\MySql;

use CrEOF\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;

/**
 * Description of Point
 *
 * @author Maximilian
 */
class Point extends AbstractSpatialDQLFunction
{
	protected $platforms = array('mysql');

    protected $functionName = 'Point';

    protected $minGeomExpr = 1;

    protected $maxGeomExpr = 1;
}
