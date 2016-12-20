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
    /**
     * @var array|\Traversable
     */
    private $rows;

    /**
     * Constructor.
     *
     * @param array|\Traversable $rows     An iterable representing the csv rows.
     * @param string             $filename An filename the client downloads.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($rows, $filename)
    {
        if (!is_array($rows) && !$rows instanceof \Traversable) {
            throw new \InvalidArgumentException('$rows should be an array or an instance of \Traversable.');
        }

        $this->rows = $rows;

        parent::__construct(array($this, 'output'), 200, array(
            'Content-Type' => 'text/csv',
        ));

        try {
            $disposition = $this->headers->makeDisposition('attachment', $filename, !preg_match('/^[\x20-\x7e]*$/', $filename) ? 'Download.csv' : '');
        } catch (\InvalidArgumentException $e) {
            $disposition = $this->headers->makeDisposition('attachment', 'Download.csv');
        }

        $this->headers->set('Content-Disposition', $disposition);
    }

    /**
     * Outputs the result.
     */
    public function output()
    {
        set_time_limit(0);

        foreach ($this->rows as $row) {
            if ($this->charset) {
                $row = $this->encodeRow($row, $this->charset);
            }

            echo implode(',', $this->wrapRowWithQuotation($row)), "\r\n";
        }
    }

    /**
     * Encodes the row data.
     *
     * @param array  $row
     * @param string $charset
     *
     * @return array
     */
    private function encodeRow(array $row, $charset)
    {
        return array_map(function ($v) use ($charset) {
            return mb_convert_encoding($v, $charset);
        }, $row);
    }

    /**
     * Wraps the column data with double quotation.
     *
     * @param array $row
     *
     * @return array
     */
    private function wrapRowWithQuotation(array $row)
    {
        return array_map(function($v) {
            return '"' . str_replace('"', '""', $v) . '"';
        }, $row);
    }
}
