<?php

namespace Upgrade\Task\Cake50;

use Upgrade\Task\FileTaskInterface;
use Upgrade\Task\Task;

/**
 * Adjusts:
 * - Remove outdated useCommandRunner() call
 */
class RemoveOutdatedCodeTask extends Task implements FileTaskInterface {

	/**
	 * @param string $path
	 *
	 * @return array<string>
	 */
	public function getFiles(string $path): array {
		return $this->collectFiles($path, 'php', ['tests/TestCase/']);
	}

	/**
	 * @param string $path
	 *
	 * @return void
	 */
	public function run(string $path): void {
		$content = (string)file_get_contents($path);
		$newContent = preg_replace('#(\s+)(\$this->useCommandRunner\(\);)#', '\1//\2', $content);

		$this->persistFile($path, $content, $newContent);
	}

}
