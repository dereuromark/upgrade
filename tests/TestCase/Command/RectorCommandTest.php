<?php


declare(strict_types=1);

namespace Cake\Upgrade\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Upgrade\Test\TestCase;

/**
 * RectorCommand test.
 */
class RectorCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var string
     */
    protected $appDir;

    /**
     * setup method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->useCommandRunner(true);
        $this->configApplication('\Cake\Upgrade\Application', []);
    }

    /**
     * @return void
     */
    public function testInvalidAppDir()
    {
        $this->exec('upgrade rector --dry-run ./something/invalid');

        $this->assertExitError();
        $this->assertErrorContains('`./something/invalid` does not exist.');
    }

    /**
     * @return void
     */
    public function testApplyAppDir()
    {
        $this->setupTestApp(__FUNCTION__);
        $this->exec('upgrade rector --dry-run ' . TEST_APP);

        $this->assertExitSuccess();
        $this->assertOutputContains('HelloCommand.php');
        $this->assertOutputContains('begin diff');
        $this->assertOutputContains('Rector applied successfully');
    }
}
