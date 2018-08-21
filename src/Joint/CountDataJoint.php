<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 11:50
 */

namespace Phore\DataPipes\Joint;


use Phore\DataPipes\Pipe\AbstractJoint;
use Phore\DataPipes\Pipe\DataSet;
use Phore\DataPipes\Pipe\JointDrain;
use Phore\DataPipes\Pipe\JointFeed;
use Phore\DataPipes\Pipe\Pipe;

class CountDataJoint extends AbstractJoint
{

    public $firstCalled = 0;
    public $messageCalled = 0;
    public $closeCalled = 0;

    public function first(DataSet $dataSet, Pipe $pipe_in)
    {
        $this->firstCalled++;
    }

    public function message(DataSet $dataSet, Pipe $pipe_in)
    {
        $this->messageCalled++;
    }

    public function close(Pipe $pipe_in)
    {
        $this->closeCalled++;
        parent::close($pipe_in);
    }
}
