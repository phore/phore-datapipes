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

require __DIR__ . "/../../vendor/autoload.php";


$csv = new CsvInputJointFeed();

$csv->getOutPipe()->connect($countJ = new CountDataJoint());





