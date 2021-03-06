<?php

namespace Issei\Tests;

use Issei\StreamedCsvResponse;
use PHPUnit\Framework\TestCase;

class StreamedCsvResponseTest extends TestCase
{
    /**
     * @test
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage $rows should be an array or an instance of \Traversable.
     */
    public function constructor_should_throw_an_InvalidArgumentException_when_non_iterable_value_is_passed_at_1st_argument()
    {
        new StreamedCsvResponse('', 'test.csv');
    }
}
