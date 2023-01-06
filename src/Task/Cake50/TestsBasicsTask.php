<?php

namespace Cake\Upgrade\Task\Cake50;

use Cake\Upgrade\Task\FileTaskInterface;
use Cake\Upgrade\Task\Task;

/**
 * Adjusts:
 * - Test extends IntegrationTestCase => Test extends TestCase
 */
class TestsBasicsTask extends Task implements FileTaskInterface
{
 /**
  * @param string $path
  *
  * @return array<string>
  */
    public function getFiles(string $path): array
    {
        return $this->collectFiles($path, 'php', ['tests/TestCase/Controller/']);
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function run(string $path): void
    {
        $content = (string)file_get_contents($path);
        $newContent = str_replace('use Cake\TestSuite\ConsoleIntegrationTestTrait;', 'use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;', $content);

        $this->persistFile($path, $content, $newContent);
    }
}
