<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 09:51
 */

namespace Phore\DataPipes\Pipe;


interface JointDrain
{

    public function getOutPipe() : Pipe;

}
