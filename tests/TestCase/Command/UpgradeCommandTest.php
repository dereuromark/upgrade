<?php
declare(strict_types=1);

namespace Upgrade\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Upgrade\Test\TestCase;

class UpgradeCommandTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	/**
	 * @return void
	 */
	public function testRun(): void {
		$this->exec('bin/cake upgrade');

		$this->assertExitCode(1);
	}

}
