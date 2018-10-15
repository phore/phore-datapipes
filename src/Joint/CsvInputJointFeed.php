<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 21.08.18
 * Time: 09:49
 */

namespace Phore\DataPipes\Joint;


use Phore\DataPipes\Pipe\DataSet;
use Phore\DataPipes\Pipe\JointDrain;
use Phore\DataPipes\Pipe\JointFeed;
use Phore\DataPipes\Pipe\Pipe;
use Phore\DataPipes\Pipe\PipeWork;
use Phore\DataPipes\Pipe\PipeWorkClient;

class CsvInputJointFeed implements JointDrain, PipeWorkClient
{

    private $outPipe;

    private $colIndex2Name = null;

    private $filePointer = null;
    private $delimiter = null;

    private $pipework;

    public function __construct(PipeWork $pipeWork)
    {
        $this->pipework = $pipeWork;
        $this->pipework->registerTick($this);

        $this->outPipe = new Pipe($this->pipework);
    }



    public function open($url, $delimiter=",")
    {
        $this->filePointer = fopen($url, "r");
        if ( ! $this->filePointer)
            throw new \InvalidArgumentException("Cannot open '$url' for reading.");
        $this->delimiter = $delimiter;
    }

    private function transformToAssoc (array $inArray) : array
    {
        $outArr = [];
        foreach ($inArray as $index => $value)
            $outArr[$this->colIndex2Name[$index]] = $value;
        return $outArr;
    }


    private function parseHeader(array $data)
    {
        foreach ($data as $index => $name) {
            $this->colIndex2Name[$index] = $name;
            $this->outPipe->define($name);
        }
    }

    public function getOutPipe(): Pipe
    {
        return $this->outPipe;
    }

    public function __on_tick(): bool
    {
        if (feof($this->filePointer))
            return false;

        $data = fgetcsv($this->filePointer, 0, $this->delimiter);

        if ($this->colIndex2Name === null) {
            $this->parseHeader($data);
        } else {
            $this->outPipe->push($this->transformToAssoc($data));
        }
        return true;
    }
}
