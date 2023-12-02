<?php

namespace Upgrade\Test\TestCase;

use Cake\TestSuite\TestCase;
use Upgrade\Filesystem\Folder;

class LevelsTest extends TestCase {

	/**
	 * Basic test to simulate running on this repo
	 *
	 * Should return all files in the src directory of this repo
	 *
	 * @return void
	 */
	public function testFiles() {
		$path = ROOT . DS . 'levels' . DS . 'cakephp' . DS;

		$folder = new Folder($path);
		$content = $folder->read();
		$files = $content[1];

		foreach ($files as $file) {
			$filePath = $path . $file;
			$yaml = yaml_parse(file_get_contents($filePath)) ?: null;
			$this->assertNotEmpty($yaml, 'File invalid: ' . $file);
		}
	}

}
