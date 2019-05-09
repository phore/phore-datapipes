<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 09.05.19
 * Time: 13:28
 */

namespace Phore\DataPipes\Helper;


use Phore\FileSystem\PhoreFile;
use Phore\FileSystem\PhoreTempFile;

class DateFile
{
    /**
     * Unix epoch of current filename
     * 
     * @var \DateTime
     */
    public $dateTime;
    
    /**
     * @var PhoreFile 
     */
    public $file;
    
    public $index;
    
    public function __construct(\DateTime $dateTime, PhoreFile $file, int $index)
    {
        $this->dateTime = $dateTime;
        $this->file = $file;
        $this->index = $index;
    }

}
