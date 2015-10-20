<?php

namespace CrEOF\Spatial\PHP\Types;

use CrEOF\Spatial\Exception\InvalidValueException;

abstract class ExtendedAbstractGeometry extends AbstractGeometry {
    
    protected function validateMultiPolygonValue(array $polygons) {
        foreach($polygons as &$polygon) {
            $polygon = $this->validatePolygonValue($polygon);
        }
        
        return $polygons;
    }
    
    private function toStringMultiPolygon(array $polygons) {
        $strings = null;
        
        foreach($polygons as $polygon) {
            $strings[] = '(' . $this->toStringPolygon($polygon) . ')';
        }
        
        return implode(',', $strings);
    }
}
