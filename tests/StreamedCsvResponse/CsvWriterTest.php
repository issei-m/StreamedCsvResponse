<?php

namespace Issei\Tests\StreamedCsvResponse;

use Issei\StreamedCsvResponse\CsvWriter;
use PHPUnit\Framework\TestCase;

class CsvWriterTest extends TestCase
{
    /**
     * @test
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage $rows should be an array or an instance of \Traversable.
     */
    public function writeRow_should_throw_an_InvalidArgumentException_when_non_iterable_is_passed()
    {
        $writer = new CsvWriter();
        $writer->writeRow('');
    }
}
