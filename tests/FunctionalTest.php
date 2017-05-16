<?php

namespace Issei\Tests;

use Issei\StreamedCsvResponse;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function rowsProvider()
    {
        $base = array(
            array('名前', 'メアド', '最近購入した商品'),
            array('田中 太郎', 'taro@example.com', '商品A,商品B,商品C'),
            array("山田 花子", 'yamada@example.com', "商品1\r\n\"商品2\"\r\n商品3"),
        );

        return array(
            'completely array' => array(
                $base,
            ),
            'using an iterable in rows' => array(
                new \ArrayIterator($base),
            ),
            'using an iterable in cells' => array(
                array(
                    new \ArrayIterator($base[0]),
                    new \ArrayIterator($base[1]),
                    new \ArrayIterator($base[2]),
                ),
            ),
            'using an iterable in both' => array(
                new \ArrayIterator(array(
                    new \ArrayIterator($base[0]),
                    new \ArrayIterator($base[1]),
                    new \ArrayIterator($base[2]),
                )),
            ),
            'cell is an object (which can be casted as a string)' => array(
                array(
                    array(new Stringable($base[0][0]), new Stringable($base[0][1]), new Stringable($base[0][2])),
                    array(new Stringable($base[1][0]), new Stringable($base[1][1]), new Stringable($base[1][2])),
                    array(new Stringable($base[2][0]), new Stringable($base[2][1]), new Stringable($base[2][2])),
                ),
            ),
        );
    }

    /**
     * @dataProvider rowsProvider
     */
    public function testGeneral($rows)
    {
        $this->expectOutputString(
            "\"名前\",\"メアド\",\"最近購入した商品\"\r\n" .
            "\"田中 太郎\",\"taro@example.com\",\"商品A,商品B,商品C\"\r\n" .
            "\"山田 花子\",\"yamada@example.com\",\"商品1\r\n\"\"商品2\"\"\r\n商品3\"\r\n"
        );

        $response = new StreamedCsvResponse($rows, 'test.csv');
        $response->sendContent();

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertEquals('attachment; filename="test.csv"', $response->headers->get('Content-disposition'));
    }

    /**
     * @dataProvider rowsProvider
     */
    public function testWithSJISWin($rows)
    {
        $this->expectOutputString(
            '"' . mb_convert_encoding('名前', 'SJIS-win', 'UTF-8') . '","' . mb_convert_encoding('メアド', 'SJIS-win', 'UTF-8') . '","' . mb_convert_encoding('最近購入した商品', 'SJIS-win', 'UTF-8') . '"' . "\r\n" .
            '"' . mb_convert_encoding('田中 太郎', 'SJIS-win', 'UTF-8') . '","taro@example.com","' . mb_convert_encoding('商品A,商品B,商品C', 'SJIS-win', 'UTF-8') . '"' . "\r\n" .
            '"' . mb_convert_encoding('山田 花子', 'SJIS-win', 'UTF-8') . '","yamada@example.com","' . mb_convert_encoding("商品1\r\n\"\"商品2\"\"\r\n商品3", 'SJIS-win', 'UTF-8') . '"' . "\r\n"
        );

        $response = new StreamedCsvResponse($rows, 'foobar.csv');
        $response->setCharset('SJIS-win');
        $response->sendContent();

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertEquals('attachment; filename="foobar.csv"', $response->headers->get('Content-disposition'));
    }

    public function filenamesProvider()
    {
        return array(
            'only ASCII' => array(
                'customers.csv',
                'attachment; filename="customers.csv"', // Expects not using url-encoded.
            ),
            'containing multi-byte' => array(
                '顧客.csv',
                'attachment; filename="Download.csv"; filename*=utf-8\'\'%E9%A1%A7%E5%AE%A2.csv'
            ),
            'containing slash' => array(
                'foo/bar.csv',
                'attachment; filename="Download.csv"', // Expects just only use fallback - no exception thrown (for BC).
            ),
        );
    }

    /**
     * @dataProvider filenamesProvider
     */
    public function testContentDisposition($filename, $expectedHeaderValue)
    {
        $response = new StreamedCsvResponse(array(), $filename);

        $this->assertEquals($expectedHeaderValue, $response->headers->get('Content-Disposition'));
    }
}

class Stringable
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var array
     */
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
