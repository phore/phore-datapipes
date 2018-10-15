<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 10:12
 */

namespace Phore\DataPipes\Queue;



class FifoQueueFuture extends AbstractFifoQueue
{

    public function walk(callable $fn) : bool {
        for ($i = 0; $i < count($this->buffer); $i++) {
            $ret = $fn($this->buffer[$i], $i);
            if ($ret === false)
                return false;
        }
        return true;
    }

    public function push($data)
    {
        array_push($this->buffer, $data);
    }

    public function isEmpty () : bool
    {
        return count($this->buffer) === 0;
    }

    public function pull()
    {
        return array_shift($this->buffer);
    }
}
