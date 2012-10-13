<?php

require __DIR__ . '/../../../../vendor/autoload.php';

error_reporting(E_ALL | E_STRICT);

$loader = new \Composer\Autoload\ClassLoader();
$loader->add('CrEOF\Spatial\Tests', __DIR__ . '/../../..');
$loader->add('Doctrine\Tests', __DIR__ . '/../../../../vendor/doctrine/orm/tests');
$loader->register();
