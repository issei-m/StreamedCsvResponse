<?php

namespace Issei\Tests;

use Issei\CsvWriter;

class CsvWriterTest extends \PHPUnit_Framework_TestCase
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
