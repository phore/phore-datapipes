<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 12:38
 */

namespace Phore\DataPipes\Pipe;


use Phore\DataPipes\Flow\Tickable;

class PipeWork
{

    /**
     * @var Tickable[]
     */
    private $tickFn = [];

    public function registerTick(Tickable $client)
    {
        $this->tickFn[] = $client;
    }

    public function singleTick() : bool
    {
        $hasTrue = false;
        foreach ($this->tickFn as $tickFn) {
            if ($tickFn->__on_tick() !== false)
                $hasTrue = true;
        }
        return $hasTrue;
    }


    public function run()
    {

    }
}
