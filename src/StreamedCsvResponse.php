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
        Assert::isIterable($rows, '$rows should be an array or an instance of \Traversable.');

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

        $writer = new CsvWriter($this->charset);

        foreach ($this->rows as $row) {
            $writer->writeRow($row);
        }
    }
}
