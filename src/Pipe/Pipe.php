<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.08.18
 * Time: 18:20
 */

namespace Phore\DataPipes\Pipe;


class Pipe
{


    /**
     * @var null|Joint
     */
    private $joint = null;

    private $numDataSets = 0;

    private $curDataSet = null;
    private $lastDataSet = null;


    private $columns = [];


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
    }

    public function push($data)
    {
        if ($data instanceof DataSet)
            $data = $data->export();
        $this->lastDataSet = $this->curDataSet;
        $this->curDataSet = $ds = new DataSet($this, $data);

        $this->numDataSets++;
        if ($this->joint !== null) {
            if ($this->numDataSets === 1)
                $this->joint->first($ds, $this);
            $this->joint->message($ds, $this);
        }
    }

    public function connect(Joint $joint)
    {
        $this->joint = $joint;
    }


    public function close()
    {
        if ($this->joint !== null)
            $this->joint->close($this);
    }

}
