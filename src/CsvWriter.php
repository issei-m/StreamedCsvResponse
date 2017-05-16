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
     * @var null
     */
    private $encodeTo;

    public function __construct($encodeTo = null)
    {
        $this->out = fopen('php://output', 'wt');

        if (null !== $encodeTo && 'UTF-8' !== strtoupper($encodeTo)) {
            $this->encodeTo = $encodeTo;
        }
    }

    public function __destruct()
    {
        fclose($this->out);
    }

    /**
     * Writes the csv to stdout.
     *
     * @param array|\Traversable $row
     */
    public function writeRow($row)
    {
        if (!is_array($row) && !$row instanceof \Traversable) {
            throw new \InvalidArgumentException('Every value of $rows should be an array or an instance of \Traversable.');
        }

        $separator = '';

        foreach ($row as $cell) {
            fwrite($this->out, $separator . $this->formatCell($cell));

            if ('' === $separator) {
                $separator = ',';
            }
        }

        fwrite($this->out, "\r\n");
    }

    /**
     * Returns the formatted cell.
     *
     * @param string $cell
     *
     * @return string
     */
    private function formatCell($cell)
    {
        // auto encoding
        if (null !== $this->encodeTo) {
            $cell = mb_convert_encoding($cell, $this->encodeTo, 'UTF-8');
        }

        // enclosing
        return '"' . str_replace('"', '""', $cell) . '"';
    }
}
