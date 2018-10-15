<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 17:29
 */

namespace Phore\DataPipes\Flow;


interface Tickable
{

    public function __on_tick();

}
