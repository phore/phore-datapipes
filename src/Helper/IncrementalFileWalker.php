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
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class IncrementalFileWalker
{


    const WALK_NEW = "new";
    const WALK_ERR = "err";


    private $dataDir;
    private $logDir;

    private $filterPreg = null;

    /**
     * @var NullLogger $logger
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

    public function setLogger(LoggerInterface $logger) : self
    {
        $this->logger = $logger;
        return $this;
    }

    protected function runSingleFile (PhoreFile $file, callable $cb)
    {
        $errFile = $this->logDir->withSubPath($file->getBasename() . ".ERR")->asFile();
//        $okFile = $this->logDir->withSubPath($file->getBasename() . ".OK")->asFile();
        $checkFile = $this->logDir->withSubPath($file->getBasename() . ".CHECK")->asFile();

        try {
            $result = $cb($file);
            if ($errFile->exists())
                $errFile->unlink();
            $checkFile->set_contents($file->fileSize());
            $this->logger->debug("Success on $file: " . phore_json_encode($result));
        } catch (\Exception $e) {
            $errFile->set_contents("ERROR: " . $e->getMessage() . "\n\n" . $e->getTraceAsString());
            $this->logger->warning("Failed: $file: " . $e->getMessage());
        }
    }

    public function walk(callable $cb, string $mode = self::WALK_NEW)
    {
        foreach ($this->dataDir->genWalk() as $file) {
            if ( ! $file->isFile())
                continue;

            $this->logger->debug("Walking $file...");
            $filename = $file->getBasename();
            if ($this->filterPreg !== null &&  ! preg_match($this->filterPreg, $filename)) {
                $this->logger->debug("Filename $filename doesn't match '$this->filterPreg' - skip file");
                continue;
            }

            if ($this->logDir->withSubPath($filename . ".OK")->isFile()) {
                $this->logger->debug("Filename $filename : OK file existing - skip");
                continue;
            }

            if ($this->logDir->withSubPath($filename . ".CHECK")->isFile()) {
                $this->logger->debug("Filename $filename : CHECK file existing - skip");
                continue;
            }

            if ($mode === self::WALK_NEW && $this->logDir->withSubPath($filename . ".ERR")->isFile()) {
                $this->logger->debug("Mode: walkNew - filename $filename : error file existing - skip");
                continue;
            }

            if ($mode === self::WALK_ERR && ! $this->logDir->withSubPath($filename . ".ERR")->isFile()) {
                $this->logger->debug("Mode: walkErr - filename $filename : error file not existing - skip");
                continue;
            }
            $this->logger->debug("Processing $filename ...");
            $this->runSingleFile($file, $cb);
        }
    }

    protected function runSingleCheckFile (PhoreFile $checkFile, callable $cb)
    {
        $inFile = $this->dataDir->withSubPath($checkFile->getFilename())->asFile();
        $okFile = $this->logDir->withSubPath($checkFile->getFilename() . ".OK")->asFile();
//        $checkFile = $this->logDir->withSubPath($file->getFilename() . ".CHECK")->asFile();

        try {
            $this->logger->notice("vor Callback");
            $result = $cb($checkFile, $inFile);
            $this->logger->notice("nach Callback");

            if($result === true) {
                $checkFile->unlink();
                $okFile->set_contents("FS checked:".$inFile->fileSize());
                $this->logger->debug("Success on filesize check: $inFile(". $inFile->fileSize().")" );
            } else {
                $checkFile->unlink();
                $okFile->unlink();
                $this->logger->debug("Failed on filesize check: $inFile(". $inFile->fileSize().")" );
            }
        } catch (\Exception $e) {
            $this->logger->notice("im Catch");
            $okFile->unlink();
            $this->logger->warning("Failed: $checkFile: " . $e->getMessage());
        }
    }

    public function walkCheck(callable $cb)
    {
        foreach ($this->logDir->genWalk() as $file) {
            if (!$file->isFile())
                continue;

            $this->logger->debug("Walking $file...");
            $filename = $file->getBasename();
            if ($this->filterPreg !== null && !preg_match($this->filterPreg, $filename)) {
                $this->logger->debug("Filename $filename doesn't match '$this->filterPreg' - skip file");
                continue;
            }
            $this->logger->debug("Processing $filename ...");
            $this->runSingleCheckFile($file, $cb);
        }
    }
}
