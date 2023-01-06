<?php


declare(strict_types=1);

namespace Cake\Upgrade\Test;

use Cake\Filesystem\Filesystem;
use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase as CakeTestCase;
use Cake\Utility\Hash;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TestCase extends CakeTestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var string
     */
    protected $testAppDir;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner(true);
    }

    /**
     * @param string $testName
     *
     * @return void
     */
    protected function setupTestApp(string $testName): void
    {
        $className = substr(static::class, strrpos(static::class, '\\') + 1, -strlen('Test'));
        $this->testAppDir = $className . '-' . $testName;
        $testAppPath = ORIGINAL_APPS . $this->testAppDir;

        if (file_exists($testAppPath)) {
            $fs = new Filesystem();
            $fs->deleteDir(TEST_APP);
            $fs->copyDir($testAppPath, TEST_APP);
        }
    }

    /**
     * @return void
     */
    protected function assertTestAppUpgraded(): void
    {
        $appFs = $this->getFsInfo(TEST_APP);
        $upgradedFs = $this->getFsInfo(UPGRADED_APPS . $this->testAppDir);
        $this->assertEquals($upgradedFs['tree'], $appFs['tree'], 'Upgraded test_app does not match `upgraded_apps`');

        foreach ($upgradedFs['files'] as $relativePath) {
            $this->assertFileEquals(UPGRADED_APPS . $this->testAppDir . DS . $relativePath, TEST_APP . $relativePath, $relativePath);
        }
    }

    /**
     * @param string $path
     *
     * @return array
     */
    protected function getFsInfo(string $path): array
    {
        if ($path[-1] !== DS) {
            $path .= DS;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $path,
                RecursiveDirectoryIterator::KEY_AS_PATHNAME |
                RecursiveDirectoryIterator::CURRENT_AS_FILEINFO |
                RecursiveDirectoryIterator::SKIP_DOTS,
            ),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        $tree = [];
        $files = [];
        foreach ($iterator as $filePath => $fileInfo) {
            $relativePath = substr($filePath, strlen($path));
            if ($fileInfo->isDir()) {
                $tree[$relativePath] = [];
            } elseif ($fileInfo->isFile() && $fileInfo->getFileName() !== 'empty') {
                $tree[$relativePath] = $fileInfo->getFileName();
                $files[] = $relativePath;
            }
        }

        return ['tree' => Hash::expand($tree, DS), 'files' => $files];
    }
}
