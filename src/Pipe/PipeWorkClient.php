<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 13:01
 */

namespace Phore\DataPipes\Pipe;


interface PipeWorkClient
{

    public function __on_tick() : bool;
    //public function __on_close_tick() : bool;

}
