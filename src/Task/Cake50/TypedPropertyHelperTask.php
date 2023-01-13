<?php

namespace Cake\Upgrade\Task\Cake50;

use Cake\Upgrade\Task\FileTaskInterface;
use Cake\Upgrade\Task\Task;

/**
 * Adjusts:
 * - protected $helpers => protected array $helpers
 */
class TypedPropertyHelperTask extends Task implements FileTaskInterface
{
    /**
     * @param string $path
     *
     * @return array<string>
     */
    public function getFiles(string $path): array
    {
        return $this->collectFiles($path, 'php', ['src/View/Helper/']);
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function run(string $path): void
    {
        $content = (string)file_get_contents($path);
        $newContent = preg_replace('#\b(public|protected) \$helpers = \[#', 'protected array $helpers = [', $content);

        $this->persistFile($path, $content, $newContent);
    }
}
