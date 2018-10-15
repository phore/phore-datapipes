<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 17:31
 */

namespace Phore\DataPipes\Flow;


use Phore\DataPipes\Pipe\DataSet;
use Phore\DataPipes\Pipe\PipeWork;
use Phore\DataPipes\Queue\FifoQueueFuture;

class AbstractFlow implements FlowDrain, FlowSource
{

    /**
     * @var FifoQueueFuture
     */
    public $futureFifo;

    /**
     * @var FlowDrain
     */
    public $nextDrain;

    public function __construct(PipeWork $pipeWork)
    {
        $this->futureFifo = new FifoQueueFuture();
        $pipeWork->registerTick($this);
    }


    public function push(DataSet $dataSet)
    {
        // TODO: Implement push() method.
    }

    public function connect(FlowDrain $drain)
    {
        $this->next = $drain;
    }

    public function __on_tick()
    {
        // TODO: Implement __on_tick() method.
    }
}
