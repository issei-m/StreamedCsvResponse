<?php

namespace Issei\StreamedCsvResponse;

/**
 * Writes the csv row to stdout.
 *
 * {@internal Don't use this in user-land code }}
 *
 * @author Issei Murasawa <issei.m7@gmail.com>
 */
class CsvWriter
{
    const SEPARATOR = ',';
    const ENCLOSURE = '"';
    const ESCAPED_ENCLOSURE = '""';

    /**
     * @var resource
     */
    private $out;

    /**
     * @var string|null
     */
    private $encodeTo;

    /**
     * @var string[]
     */
    private static $charsNeedingEnclosing = array(
        self::SEPARATOR,
        self::ENCLOSURE,
        "\n",
        "\r",
    );

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
        Assert::isIterable($row, 'Every value of $rows should be an array or an instance of \Traversable.');

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
        if ('' === $cell) {
            return $cell;
        }

        // auto encoding
        if (null !== $this->encodeTo) {
            $cell = mb_convert_encoding($cell, $this->encodeTo, 'UTF-8');
        }

        foreach (self::$charsNeedingEnclosing as $charNeedingEnclosing) {
            if (false !== strpos($cell, $charNeedingEnclosing)) {
                // enclosing
                return self::ENCLOSURE . str_replace(self::ENCLOSURE, self::ESCAPED_ENCLOSURE, $cell) . self::ENCLOSURE;
            }
        }

        return $cell;
    }
}
