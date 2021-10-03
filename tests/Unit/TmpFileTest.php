<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\PhpProcess;
use App\Lib\Filesystem\TmpFile\TmpFile;


class TmpFileTest extends TestCase
{
    public function testCreateTmpFile(): void
    {
        $tmpFile = new TmpFile(true);
        $this->assertFileExists($tmpFile->getPath());
    }


    public function testRemoveTmpFileOnGarbageCollection(): void
    {
        $callback = function () use (&$filename) {
            $filename = (string) new TmpFile(true);
        };

        $callback();

        $this->assertFileDoesNotExist($filename);
    }


    public function testExistsTmpFileOnGarbageCollection(): void
    {
        $callback = function () use (&$filename) {
            $filename = (string) new TmpFile(false);
        };

        $callback();

        $this->assertFileExists($filename);
    }


    public function testRemoveTmpFileOnFatalError(): void
    {
        $fatalErrorUseCase = $this->getFatalErrorUseCase();

        $process = new PhpProcess($fatalErrorUseCase, __DIR__);

        $process->run();

        $output = $process->getOutput();

        $data = explode(PHP_EOL, $output);

        $this->assertStringContainsString(sys_get_temp_dir(), $data[0]);
        $this->assertFileDoesNotExist($data[0]);
    }

    private function getFatalErrorUseCase(): string
    {
        return <<<'EOF'
<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use App\Lib\Filesystem\TmpFile\TmpFile;
$tmpFile = new TmpFile(true);
echo $tmpFile;
trigger_error('Fatal error!', E_USER_ERROR);
EOF;
    }
}
