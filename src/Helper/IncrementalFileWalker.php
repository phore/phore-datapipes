<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 28.11.19
 * Time: 11:01
 */

namespace Phore\DataPipes\Helper;


use Phore\FileSystem\PhoreDirectory;
use Phore\FileSystem\PhoreFile;
use Psr\Log\NullLogger;

class IncrementalFileWalker
{


    const WALK_NEW = "new";
    const WALK_ERR = "err";


    private $dataDir;
    private $logDir;

    private $filterPreg = null;

    /**
     * @var NullLogger
     */
    private $logger;


    public function __construct(PhoreDirectory $dataDir, PhoreDirectory $logDir)
    {
        $dataDir->assertDirectory();
        $logDir->assertDirectory(true);

        $this->dataDir = $dataDir;
        $this->logDir = $logDir;

        $this->logger = new NullLogger();
    }

    public function setFileFilterPreg($filterPreg) {
        $this->filterPreg = $filterPreg;
        if (preg_match($filterPreg, "") === false)
            throw new \InvalidArgumentException("Invalid regular expression: '$filterPreg'");
    }





    protected function runSingleFile (PhoreFile $file, callable $cb)
    {
        $errFile = $this->logDir->withSubPath($file->getFilename() . ".ERR")->asFile();
        $okFile = $this->logDir->withSubPath($file->getFilename() . ".OK")->asFile();

        try {
            $result = $cb($file);
            if ($errFile->exists())
                $errFile->unlink();
            $okFile->set_contents(phore_json_encode($result));
            $this->logger->info("Success on $file: " . phore_json_encode($result));
        } catch (\Exception $e) {
            $errFile->set_contents("ERROR: " . $e->getMessage() . "\n\n" . $e->getTraceAsString());
            $this->logger->emergency("Failed: $file: " . $e->getMessage());
        }
    }

    public function walk(callable $cb, string $mode = self::WALK_NEW)
    {
        foreach ($this->dataDir->genWalk() as $file) {
            $filename = $file->getFilename();
            if ($this->filterPreg !== null &&  ! preg_match($this->filterPreg, $filename))
                continue;

            if ($this->logDir->withSubPath($filename . ".OK")->isFile()) {
                continue;
            }

            if ($mode === self::WALK_NEW && $this->logDir->withSubPath($filename . ".ERR")->isFile())
                continue;

            if ($mode === self::WALK_ERR && ! $this->logDir->withSubPath($filename . ".ERR")->isFile())
                continue;

            $this->runSingleFile($file, $cb);

        }
    }




}
