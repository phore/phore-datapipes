<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.08.18
 * Time: 18:20
 */

namespace Phore\DataPipes\Pipe;



use Phore\DataPipes\Queue\FifoQueueFuture;
use Phore\DataPipes\Queue\FifoQueueHistory;

class Pipe
{


    /**
     * @var null|JointFeed
     */
    private $joint = null;

    private $numDataSets = 0;

    private $curDataSet = null;

    private $columns = [];

    /**
     * @var FifoQueueFuture
     */
    private $futureFifo;

    /**
     * @var FifoQueueHistory
     */
    private $historyFifo;


    public function __construct()
    {
        $this->futureFifo = new FifoQueueFuture(0);
        $this->historyFifo = new FifoQueueHistory(1);

        $this->futureFifo->setNext(function (DataSet $curDataSet) {
            if ($this->joint !== null) {
                if ($this->numDataSets === 1)
                    $this->joint->first($curDataSet, $this);
                $this->joint->message($curDataSet, $this);

            }
            $this->historyFifo->push($curDataSet);
        });

    }


    public function future() : FifoQueueFuture
    {
        return $this->futureFifo;
    }

    public function history() : FifoQueueHistory
    {
        return $this->historyFifo;
    }



    /**
     * Define a column in the DataSet
     *
     * @param string $column
     * @param callable|null $createFn
     */
    public function define(string $column, callable $createFn=null)
    {
        if (isset ($this->columns[$column]))
            throw new \InvalidArgumentException("Column '$column' is already defined");
        $this->columns[$column] = [
            "createFn" => $createFn
        ];
    }

    public function push($data)
    {
        if ($data instanceof DataSet)
            $data = $data->export();
        $this->curDataSet = $ds = new DataSet($this, $data);
        $this->numDataSets++;
        $this->futureFifo->push($ds);
    }

    public function connect(JointFeed $joint)
    {
        $this->joint = $joint;
    }


    public function close()
    {
        $this->futureFifo->close();
        if ($this->joint !== null)
            $this->joint->close($this);
        $this->historyFifo->close();
    }

}
