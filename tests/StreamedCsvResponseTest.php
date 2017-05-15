<?php

namespace Issei\Tests;

use Issei\StreamedCsvResponse;

class StreamedCsvResponseTest extends \PHPUnit_Framework_TestCase
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
