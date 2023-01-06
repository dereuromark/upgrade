<?php


declare(strict_types=1);

namespace Cake\Upgrade\Test\TestCase\Command;

use Cake\Core\Configure;
use Cake\Upgrade\Test\TestCase;

/**
 * FileRenameCommand test.
 */
class FileRenameCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testTemplates(): void
    {
        $this->setupTestApp(__FUNCTION__);
        Configure::write('App.paths.plugins', TEST_APP . 'plugins');

        $this->exec('upgrade file_rename templates ' . TEST_APP);
        $this->assertTestAppUpgraded();
    }

    /**
     * @return void
     */
    public function testLocales(): void
    {
        $this->setupTestApp(__FUNCTION__);
        Configure::write('App.paths.plugins', TEST_APP . 'plugins');

        $this->exec('upgrade file_rename locales ' . TEST_APP);
        $this->assertTestAppUpgraded();
    }
}
