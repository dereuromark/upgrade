<?php

namespace Upgrade\Task\Cake50;

use Upgrade\Task\FileTaskInterface;
use Upgrade\Task\Task;

/**
 * Adjusts:
 * - protected $_defaultConfig => protected array $_defaultConfig
 */
class TypedPropertyFixtureTask extends Task implements FileTaskInterface {

	/**
	 * @param string $path
	 *
	 * @return array<string>
	 */
	public function getFiles(string $path): array {
		return $this->collectFiles($path, 'php', ['tests/Fixture/']);
	}

	/**
	 * @param string $path
	 *
	 * @return void
	 */
	public function run(string $path): void {
		$content = (string)file_get_contents($path);

		$from = [
			'#\bpublic \$fields = \[#',
			'#\bpublic \$records = \[#',
		];
		$to = [
			'public array $fields = [',
			'public array $records = [',
		];
		$newContent = preg_replace($from, $to, $content);

		$this->persistFile($path, $content, $newContent);
	}

}
