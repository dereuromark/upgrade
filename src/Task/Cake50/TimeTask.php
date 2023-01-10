<?php

namespace Cake\Upgrade\Task\Cake50;

use Cake\Upgrade\Task\FileTaskInterface;
use Cake\Upgrade\Task\Task;

/**
 * Adjusts:
 * - Cake\I18n\FrozenTime to Cake\I18n\DateTime
 */
class TimeTask extends Task implements FileTaskInterface
{
    /**
     * @param string $path
     *
     * @return array<string>
     */
    public function getFiles(string $path): array
    {
        return $this->collectFiles($path, 'php', ['src/', 'tests/TestCase/', 'config/']);
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function run(string $path): void
    {
        $content = (string)file_get_contents($path);

        // Skip on conflicts with the use statements for now
        if (str_contains($content, 'use DateTime;')) {
            return;
        }

        $newContent = preg_replace('#\bCake\\\\I18n\\\\FrozenTime\b#', 'Cake\I18n\DateTime', $content);
        $newContent = preg_replace('#\bFrozenTime\b#', 'DateTime', $content);

        $this->persistFile($path, $content, $newContent);
    }
}
