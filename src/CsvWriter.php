<?php

namespace Issei;

/**
 * Writes the csv row to stdout.
 *
 * {@internal Don't use this in user-land code }}
 *
 * @author Issei Murasawa <issei.m7@gmail.com>
 */
class CsvWriter
{
    /**
     * @var resource
     */
    private $out;

    /**
     * @var callable
     */
    private $columnFilter;

    public function __construct($columnFilter = null)
    {
        $this->out = fopen('php://output', 'wt');
        $this->columnFilter = $columnFilter;
    }

    public function __destruct()
    {
        fclose($this->out);
    }

    /**
     * WRites the csv to stdout.
     *
     * @param array|\Traversable $row
     */
    public function writeRow($row)
    {
        if (!is_array($row) && !$row instanceof \Traversable) {
            throw new \InvalidArgumentException('Every value of $rows should be an array or an instance of \Traversable.');
        }

        $startedTraverse = false;

        foreach ($row as $column) {
            if ($startedTraverse) {
                fwrite($this->out, ',');
            } else {
                $startedTraverse = true;
            }

            if ($this->columnFilter) {
                $column = call_user_func($this->columnFilter, $column);
            }

            fwrite($this->out, self::encloseColumn($column));
        }

        fwrite($this->out, "\r\n");
    }

    /**
     * Encloses the column value.
     *
     * @param string $column
     *
     * @return string
     */
    private static function encloseColumn($column)
    {
        return '"' . str_replace('"', '""', $column) . '"';
    }
}
