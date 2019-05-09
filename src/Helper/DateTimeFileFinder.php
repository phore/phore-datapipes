<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 26.04.19
 * Time: 13:02
 */

namespace Phore\DataPipes\Helper;


use Phore\FileSystem\PhoreFile;
use Phore\FileSystem\PhoreUri;

class DateTimeFileFinder
{

    /**
     * @var PhoreUri
     */
    private $cwd;

    /**
     * DateTimeFileFinder constructor.
     * @param $cwd  string|PhoreUri     The directory to search for files
     */
    public function __construct($cwd)
    {
        if ( ! $cwd instanceof PhoreUri)
            $cwd = new PhoreUri($cwd);
        $this->cwd = $cwd;
    }

    /**
     * @var null | array
     */
    private $fileList = null;

    /**
     * Search for filenames matching the given format
     *
     * Ignores files, that are not scanned
     *
     * @param string $format
     * @return DateTimeFileFinder
     * @throws \Phore\FileSystem\Exception\FileAccessException
     * @throws \Phore\FileSystem\Exception\FilesystemException
     */
    public function loadFileNames(string $format, bool $strict=false)  : self
    {
        $this->cwd->assertDirectory()->walk(function(PhoreUri $uri) use ($format, $strict) {
            if ( ! $uri instanceof PhoreFile)
                return;

            $ts = \DateTime::createFromFormat($format, $uri->getBasename());
            if ($ts === false) {
                if ($strict === true)
                    throw new \Exception("Unrecognized filename '$uri' by format '$format': " . implode ("; ", \DateTime::getLastErrors()["errors"]));
                phore_out("Ignore: Unrecognized filename '$uri' by format '$format': " . implode("; ", \DateTime::getLastErrors()["errors"]) );
                return;
            }

            $this->fileList[] = [$ts, $uri];
        });
        return $this;
    }


    public function skipFileNameMatch(string $preg) : self
    {
        
        return $this;
    }


    public function sort($reverse=false) : self
    {
        $cmpFn = function ($a, $b) use ($reverse) {
            if ($a[0]->getTimestamp() == $b[0]->getTimestamp()) {
                return 0;
            }
            $ret = ($a[0]->getTimestamp() < $b[0]->getTimestamp()) ? -1 : 1;
            if ($reverse === false)
                return $ret;
            return (-1) * $ret;
        };

        usort($this->fileList, $cmpFn);
        return $this;
    }


    /**
     *
     *
     * <example>
     * $series->walk(
     *      function(DateFile $current, DateFile $previous=null, DateFile $next=null) {
     *          // do something
     *      }
     * );
     * </example>
     *
     * @param callable $fn
     */
    public function walk (callable $fn)
    {
        foreach ($this->fileList as $index => $cur) {
            $previous = isset($this->fileList[$index-1]) ? new DateFile($this->fileList[$index-1][0], $this->fileList[$index-1][1], $index-1) : null; 
            $next = isset($this->fileList[$index+1]) ? new DateFile($this->fileList[$index+1][0], $this->fileList[$index+1][1], $index+1) : null;
            $fn(new DateFile($cur[0], $cur[1], $index), $previous, $next);
        }
    }

}
