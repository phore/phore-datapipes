<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 11:52
 */

namespace Phore\DataPipes\Pipe;


abstract class AbstractJoint implements JointDrain, JointFeed
{
    /**
     * @var Pipe
     */
    protected $outPipe;

    public function __construct()
    {
        $this->outPipe = new Pipe();
    }

    public function getOutPipe(): Pipe
    {
        return $this->outPipe;
    }

    public function close(Pipe $pipe_in)
    {
        $this->outPipe->close();
    }

}
