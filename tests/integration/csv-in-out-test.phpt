<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 11:43
 */

namespace Test;
use Phore\DataPipes\Joint\CountDataJoint;
use Phore\DataPipes\Joint\CsvInputJointFeed;
use Phore\DataPipes\Pipe\PipeWork;
use Tester\Assert;

require __DIR__ . "/../../vendor/autoload.php";

$pipeWork = new PipeWork();

$csv = new CsvInputJointFeed();

$csv->getOutPipe()->connect($countJ = new CountDataJoint());


$csv->open(__DIR__ . "/mock/test.csv");
$csv->read();

Assert::equal(0, $countJ->firstCalled);

$csv->read();

Assert::equal(1, $countJ->firstCalled);
Assert::equal(1, $countJ->messageCalled);
Assert::equal(0, $countJ->closeCalled);

$csv->read();


