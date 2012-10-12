<?php
/**
 * Copyright (C) 2012 Derek J. Lambert
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

namespace CrEOF\PHP\Types\Spatial\Geometry;

use CrEOF\Exception\InvalidValueException;
use CrEOF\PHP\Types\Spatial\Geometry;

/**
 * Point object for POINT geometry type
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class Point extends Geometry
{
    /**
     * @var float $latitude
     */
    protected $latitude;

    /**
     * @var float $longitude
     */
    protected $longitude;

    /**
     * @param mixed $latitude
     * @param mixed $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->setLatitude($latitude)
            ->setLongitude($longitude);
    }

    /**
     * @param mixed $latitude
     *
     * @return self
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $this->toFloat($latitude);

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $longitude
     *
     * @return self
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $this->toFloat($longitude);

        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::POINT;
    }

    /**
     * @param mixed $value
     *
     * @return float
     */
    private function toFloat($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        return $this->convertStringToFloat($value);
    }

    /**
     * @param string $value
     *
     * @return float
     * @throws InvalidValueException
     */
    private function convertStringToFloat($value)
    {
        $regex = <<<EOD
/
^                                         # beginning of string
(?|
    (?|
        (?<degrees>[0-8]?[0-9])           # degrees 0-89
        (?::|°\s*)                        # colon or degree and optional spaces
        (?<minutes>[0-5]?[0-9])           # minutes 0-59
        (?::|(?:\'|\xe2\x80\xb2)\s*)      # colon or minute or apostrophe and optional spaces
        (?<seconds>[0-5]?[0-9](?:\.\d+)?) # seconds 0-59 and optional decimal
        (?:(?:"|\xe2\x80\xb3)\s*)?        # quote or double prime and optional spaces
        |
        (?<degrees>90)(?::|°\s*)(?<minutes>0?0)(?::|(?:\'|\xe2\x80\xb2)\s*)(?<seconds>0?0)(?:(?:"|\xe2\x80\xb3)\s*)?
    )
    (?<direction>[NnSs])                  # N or S for latitude
    |
    (?|
        (?<degrees>0?[0-9]?[0-9]|1[0-7][0-9]) # degrees 0-179
        (?::|°\s*)                            # colon or degree and optional spaces
        (?<minutes>[0-5]?[0-9])               # minutes 0-59
        (?::|(?:\'|\xe2\x80\xb2)\s*)          # colon or minute or apostrophe and optional spaces
        (?<seconds>[0-5]?[0-9](?:\.\d+)?)     # seconds 0-59 and optional decimal
        (?:(?:"|\xe2\x80\xb3)\s*)?            # quote or double prime and optional spaces
        |
        (?<degrees>180)(?::|°\s*)(?<minutes>0?0)(?::|(?:\'|\xe2\x80\xb2)\s*)(?<seconds>0?0)(?:(?:"|\xe2\x80\xb3)\s*)?
    )
    (?<direction>[EeWw])                      # E or W for latitude
)
$                                             # end of string
/x
EOD;

        switch (1) {
            case preg_match_all($regex, $value, $matches, PREG_SET_ORDER):
                break;
            default:
                throw new InvalidValueException($value . ' is not a valid coordinate value.');
        }

        $p = $matches[0];

        return ($p['degrees'] + ((($p['minutes'] * 60) + $p['seconds']) / 3600)) * (float) $this->getDirectionSign($p['direction']);
    }

    /**
     * @param string $direction
     *
     * @return int
     */
    private function getDirectionSign($direction)
    {
        switch (strtolower($direction)) {
            case 's':
            case 'w':
                return -1;
                break;
            case 'n':
            case 'e':
                return 1;
                break;
        }
    }
}
