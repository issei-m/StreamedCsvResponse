<?php

namespace Issei\Tests;

use Issei\StreamedCsvResponse;

class StreamedCsvResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneral()
    {
        $rows = array(
            array('名前', 'メアド', '性別',),
            array('田中 太郎', 'taro@example.com', '男性',),
        );

        $this->expectOutputString('"名前","メアド","性別"' . "\r\n" . '"田中 太郎","taro@example.com","男性"' . "\r\n");

        $response = new StreamedCsvResponse($rows, 'test.csv');
        $response->sendContent();

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertEquals('attachment; filename=test.csv', $response->headers->get('Content-disposition'));
    }

    public function testWithShiftJIS()
    {
        $rows = array(
            array('名前',),
            array('田中 太郎',),
        );

        $this->expectOutputString('"' . mb_convert_encoding('名前', 'Shift-JIS', 'UTF-8') . '"' . "\r\n" . '"' . mb_convert_encoding('田中 太郎', 'Shift-JIS', 'UTF-8') . '"' . "\r\n");

        $response = new StreamedCsvResponse($rows, 'foobar.csv');
        $response->setCharset('Shift-JIS');
        $response->sendContent();

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertEquals('attachment; filename=foobar.csv', $response->headers->get('Content-disposition'));
    }

    /**
     * @test
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage $rows should be an array or an instance of \Traversable.
     */
    public function constructor_should_throw_an_InvalidArgumentException_if_not_traversable_value_given()
    {
        new StreamedCsvResponse('', 'test.csv');
    }
}
