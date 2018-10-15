<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 17:27
 */

namespace Phore\DataPipes\Flow;


use Phore\DataPipes\Pipe\DataSet;

interface FlowDrain extends Tickable
{
    public function push(DataSet $dataSet);
}
