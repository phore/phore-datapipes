<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 17:26
 */

namespace Phore\DataPipes\Flow;


interface FlowSource extends Tickable
{
    public function connect(FlowDrain $drain);
}
