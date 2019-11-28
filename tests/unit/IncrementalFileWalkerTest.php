<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 28.11.19
 * Time: 12:43
 */

namespace Test;

use Phore\DataPipes\Helper\IncrementalFileWalker;
use Phore\FileSystem\PhoreFile;
use PHPUnit\Framework\TestCase;

/**
 * Class IncrementalFileWalkerTest
 * @package Test
 * @internal
 */
class IncrementalFileWalkerTest extends TestCase
{


    public function testBasicUsage()
    {

        phore_file("/tmp/IN/test.in")->createPath()->set_contents("abc");
        $logDir = phore_dir("/tmp/LOG")->rmDir(true);

        $walker = new IncrementalFileWalker(phore_dir("/tmp/IN"), $logDir);

        $proc = 0;
        $walker->walk(function(PhoreFile $inFile) use (&$proc) {
            $proc++;
            return true;

        });
        $this->assertEquals(1, $proc);

        $walker->walk(function(PhoreFile $inFile) use (&$proc) {
            $proc++;
            return true;

        });
        $this->assertEquals(1, $proc);

    }


}
