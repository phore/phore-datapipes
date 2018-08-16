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
    }


    private function ts() : int
    {

    }

    public function col(string $name)
    {

    }

    public function getInt(string $name)
    {

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
