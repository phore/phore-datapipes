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

class CsvInputJointFeed implements JointDrain
{

    private $pipe;

    private $colIndex2Name = null;

    public function __construct()
    {
        $this->pipe = new Pipe();
    }


    public function parse($url, $delimiter=",")
    {
        $fp = fopen($url, "r");
        if ( ! $fp)
            throw new \InvalidArgumentException("Cannot open '$url' for reading.");

        while ( ! feof($fp)) {
            $data = fgetcsv($fp, 0, $delimiter);
            if ($this->colIndex2Name === null) {
                $this->parseHeader($data);
            } else {
                $this->pipe->push($this->transformToAssoc($data));
            }
        }
        fclose($fp);
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
            $this->pipe->define($name);
        }
    }

    public function getOutPipe(): Pipe
    {
        return $this->pipe;
    }
}
