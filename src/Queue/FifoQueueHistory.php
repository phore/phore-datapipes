<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 10:57
 */

namespace Phore\DataPipes\Queue;


class FifoQueueHistory extends AbstractFifoQueue
{

    public function walk(callable $fn) : bool {
        for ($i =0; $i < count($this->buffer); $i++) {
            $ret = $fn($this->buffer[$i], $i++);
            if ($ret === false)
                return false;
        }
        return true;
    }

    public function push($data)
    {
        array_unshift($this->buffer, $data);
        if (count($this->buffer) > $this->length) {
            $dataset = array_pop($this->buffer);
            if ($this->next !== null) {
                ($this->next)($dataset);
            }
        }
    }

    public function close()
    {
        while(count($this->buffer) > 0) {
            $dataset = array_pop($this->buffer);
            if ($this->next !== null) {
                ($this->next)($dataset);
            }
        }
    }

}
