<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 26.04.19
 * Time: 15:31
 */

namespace Test;


use Phore\DataPipes\InputFormat\CsvInput;
use PHPUnit\Framework\TestCase;

class CsvInputTest extends TestCase
{



    public function testReadingIndexedWithGenerator()
    {
        $csv = new CsvInput(__DIR__ . "/CsvInputTestMock/input_with_header.csv");
        $a = [];
        foreach ($csv->getData() as $data) {
            $a[] = $data;
        }
        $this->assertEquals(3, count($a));
        $this->assertEquals("name", $a[0][0]);
        $this->assertEquals("d2", $a[2][2]);
    }

    public function testReadingHeader()
    {
        $csv = new CsvInput(__DIR__ . "/CsvInputTestMock/input_with_header.csv");
        $csv->readHeader();
        $a = [];
        foreach ($csv->getData() as $data) {
            $a[] = $data;
        }
        $this->assertEquals(2, count($a));
        $this->assertEquals("n1", $a[0]["name"]);
        $this->assertEquals("d2", $a[1]["date"]);
    }

    public function testReadingSkipComments()
    {
        $csv = new CsvInput(__DIR__ . "/CsvInputTestMock/input_with_comments.csv", ",", ["#"]);
        $csv->setIgnoreLinesStartingWith(["#"])->readHeader();
        $a = [];
        foreach ($csv->getData() as $data) {
            $a[] = $data;
        }
        $this->assertEquals(2, count($a));
        $this->assertEquals("n1", $a[0]["name"]);
    }

    public function testDoubleCallingGetDataTriggersException()
    {
        $csv = new CsvInput(__DIR__ . "/CsvInputTestMock/input_with_comments.csv", ",", ["#"]);
        $csv->setIgnoreLinesStartingWith(["#"])->readHeader();
        $a = [];
        foreach ($csv->getData() as $data) {
            $a[] = $data;
        }
        $this->expectException(\InvalidArgumentException::class, "The file is already closed. Use getData() only once!");
        foreach ($csv->getData() as $data) {
            $a[] = $data;
        }
    }
}
