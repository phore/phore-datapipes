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
    protected $minLenght;

    protected $buffer = [];

    protected $next = null;

    public function __construct(int $minLenght = 0)
    {
        $this->minLenght = $minLenght;
    }

    public function setMinLength(int $minLength)
    {
        $this->minLenght = $minLength;
    }

    public function setNext(callable $fn)
    {
        $this->next = $fn;
    }

    public function isEmpty() : bool
    {
        return count($this->buffer) === 0;
    }

    public function isValid() : bool
    {
        return count($this->buffer) >= $this->minLenght;
    }


    abstract public function walk(callable $fn) : bool;

    abstract public function push($data);

    abstract public function pull();

}
