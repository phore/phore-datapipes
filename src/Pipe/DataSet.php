<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.08.18
 * Time: 18:20
 */

namespace Phore\DataPipes\Pipe;


class DataSet
{

    /**
     * @var Pipe
     */
    private $pipe;

    private $data;


    public function __construct(Pipe $ownerPipe, array $data)
    {
        $this->pipe = $ownerPipe;
        $this->data = $data;
    }


    private function ts() : int
    {
        return (int)$this->data["ts"];
    }

    public function col(string $name)
    {
        if ( ! isset ($this->data[$name]))
            throw new \InvalidArgumentException("Column '$name' is undefined");
        return $this->data[$name];
    }

    public function getInt(string $name)
    {
        return (int)$this->col($name);
    }

    public function future(int $index=1)
    {

    }

    public function history(int $index=1)
    {

    }


    public function export() : array
    {

    }

}
