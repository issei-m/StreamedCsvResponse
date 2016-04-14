<?php

namespace Issei\Tests;

use Issei\StreamedCsvResponse;

class Stringable
{
    private $string;
    private $memoryOverflowMaker;

    public function __construct($string)
    {
        $this->string = $string;

        // To test whether no error occurs when recursion is passed to response.
        // See: https://bugs.php.net/bug.php?id=66964
        $mof = array();
        $mof[] = &$mof;
        $this->memoryOverflowMaker = $mof;
    }

    public function __toString()
    {
        return $this->string;
    }
}

class StreamedCsvResponseTest extends \PHPUnit_Framework_TestCase
{
    public function rowsProvider()
    {
        return array(
            'array' => array(
                array(
                    array('名前', 'メアド', '性別'),
                    array('田中 太郎', 'taro@example.com', '男性'),
                ),
            ),
            'Traversable' => array(
                new \ArrayIterator(array(
                    array('名前', 'メアド', '性別'),
                    array('田中 太郎', 'taro@example.com', '男性'),
                )),
            ),
            'containing Stringable' => array(
                array(
                    array(new Stringable('名前'), new Stringable('メアド'), new Stringable('性別')),
                    array(new Stringable('田中 太郎'), new Stringable('taro@example.com'), new Stringable('男性')),
                ),
            ),
        );
    }

    /**
     * @dataProvider rowsProvider
     */
    public function testGeneral($rows)
    {
        $this->expectOutputString('"名前","メアド","性別"' . "\r\n" . '"田中 太郎","taro@example.com","男性"' . "\r\n");

        $response = new StreamedCsvResponse($rows, 'test.csv');
        $response->sendContent();

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertEquals('attachment; filename=test.csv', $response->headers->get('Content-disposition'));
    }

    /**
     * @dataProvider rowsProvider
     */
    public function testWithShiftJIS($rows)
    {
        $this->expectOutputString(
            '"' . mb_convert_encoding('名前', 'Shift-JIS', 'UTF-8') . '",' .
            '"' . mb_convert_encoding('メアド', 'Shift-JIS', 'UTF-8') . '",' .
            '"' . mb_convert_encoding('性別', 'Shift-JIS', 'UTF-8') . '"' . "\r\n" .
            '"' . mb_convert_encoding('田中 太郎', 'Shift-JIS', 'UTF-8') . '",' .
            '"taro@example.com",' .
            '"' . mb_convert_encoding('男性', 'Shift-JIS', 'UTF-8') . '"' . "\r\n"
        );

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
