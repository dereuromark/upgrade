<?php

namespace Cake\Upgrade\Task\Cake50;

use Cake\Upgrade\Task\FileTaskInterface;
use Cake\Upgrade\Task\Task;

/**
 * Adjusts:
 * - new Controller() to new Controller(new Request())
 */
class TestsControllerInstantiationTask extends Task implements FileTaskInterface
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
        $newContent = preg_replace('# = new Controller\(\)#', ' = new Controller(new \Cake\Http\ServerRequest())', $content);

        $newContent = preg_replace('#\bnew ComponentRegistry\(\)#', 'new ComponentRegistry(new \Cake\Controller\Controller(new \Cake\Http\ServerRequest()))', $newContent);
        $newContent = preg_replace('#Test extends IntegrationTestCase\b#', 'Test extends \Cake\TestSuite\TestCase', $newContent);

        $this->persistFile($path, $content, $newContent);
    }
}
