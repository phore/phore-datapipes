<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.08.18
 * Time: 18:38
 */

namespace Phore\DataPipes\Pipe;


interface JointFeed
{

    public function first(DataSet $dataSet, Pipe $pipe_in);

    public function message(DataSet $dataSet, Pipe $pipe_in);

    public function close(Pipe $pipe_in);

}
