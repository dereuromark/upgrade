<?php
declare(strict_types=1);

namespace Upgrade\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Upgrade\Test\TestCase;

class FileUpgradeCommandTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	/**
	 * @return void
	 */
	public function testRun(): void {
		$this->exec('bin/cake upgrade files');

		$this->assertExitCode(1);
	}

}
