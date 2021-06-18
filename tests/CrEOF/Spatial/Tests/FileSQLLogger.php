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

namespace CrEOF\Spatial\Tests;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * Simple SQLLogger to log to file.
 */
class FileSQLLogger implements SQLLogger
{
    /**
     * Filename.
     *
     * @var string
     */
    protected $filename;

    /**
     * FileSQLLogger constructor.
     *
     * @param string $filename the filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Logs a SQL statement somewhere.
     *
     * @param string              $sql    the SQL to be executed
     * @param mixed[]|null        $params the SQL parameters
     * @param int[]|string[]|null $types  the SQL parameter types
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        file_put_contents($this->filename, $sql.PHP_EOL, FILE_APPEND);

        if ($params) {
            file_put_contents($this->filename, var_export($params, true).PHP_EOL, FILE_APPEND);
        }

        if ($types) {
            file_put_contents($this->filename, var_export($types, true).PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * Marks the last started query as stopped. This can be used for timing of queries.
     */
    public function stopQuery()
    {
    }
}
