<?php

namespace Issei;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Represents a CSV format file as streamed HTTP response.
 *
 * @author Issei Murasawa <issei.m7@gmail.com>
 */
class StreamedCsvResponse extends StreamedResponse
{
    private $rows;

    /**
     * Constructor.
     *
     * @param  array|\Traversable $rows
     * @param  string             $filename
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($rows, $filename)
    {
        if (!is_array($rows) && !$rows instanceof \Traversable) {
            throw new \InvalidArgumentException('$rows should be an array or an instance of \Traversable.');
        }

        $this->rows = $rows;

        parent::__construct([$this, 'output'], 200, [
            'Content-Type'        => 'text/csv',
            'Content-disposition' => 'attachment; filename=' . $filename
        ]);
    }

    /**
     * Outputs the result.
     */
    public function output()
    {
        set_time_limit(0);

        foreach ($this->rows as $row) {
            if ($this->charset) {
                $row = $this->encodeRow($row);
            }

            echo implode(',', $this->wrapRowWithQuotation($row)), "\r\n";
        }
    }

    /**
     * Encodes the row data.
     *
     * @param  array $row
     * @return array
     */
    private function encodeRow(array $row)
    {
        mb_convert_variables($this->charset, 'UTF-8', $row);

        return $row;
    }

    /**
     * Wraps the column data with double quotation.
     *
     * @param  array $row
     * @return array
     */
    private function wrapRowWithQuotation(array $row)
    {
        return array_map(function($string) {
            return '"' . str_replace('"', '""', $string) . '"';
        }, $row);
    }
}
