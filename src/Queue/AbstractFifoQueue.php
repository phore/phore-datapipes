<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 10:59
 */

namespace Phore\DataPipes\Queue;


abstract class AbstractFifoQueue
{
    protected $length;

    protected $buffer = [];

    protected $next = null;

    public function __construct(int $length = 0)
    {
        $this->length = $length;
    }

    public function setLength(int $length)
    {
        $this->length = $length;
    }

    public function setNext(callable $fn)
    {
        $this->next = $fn;
    }

    abstract public function walk(callable $fn) : bool;

    abstract public function push($data);

    abstract public function close();

}
