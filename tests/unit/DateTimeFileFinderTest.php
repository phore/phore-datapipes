<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 26.04.19
 * Time: 13:09
 */

namespace Test;


use Phore\DataPipes\Helper\DateTimeFileFinder;
use PHPUnit\Framework\TestCase;

class DateTimeFileFinderTest extends TestCase
{


    public function testFindDatesInCorrectOrder()
    {
        $finder = new DateTimeFileFinder(__DIR__ . "/DateTimeFileFinderMock");

        $finder->loadFileNames('\x\y_Y-m-d-H:i:s.\c\s\v');
        $finder->sort();

    }

}
