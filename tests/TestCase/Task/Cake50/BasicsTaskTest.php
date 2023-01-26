<?php

namespace Cake\Upgrade\Test\TestCase\Task\Cake50;

use Cake\TestSuite\TestCase;
use Cake\Upgrade\Task\Cake50\BasicsTask;

class BasicsTaskTest extends TestCase
{
 /**
  * Basic test to simulate running on this repo
  *
  * Should return all files in the src directory of this repo
  *
  * @return void
  */
    public function testRun()
    {
        $path = TESTS . 'test_files' . DS . 'Task' . DS . 'Cake50' . DS;
        $filePath = $path . 'src' . DS . 'Controller' . DS . 'SomeController.php';

        $task = new BasicsTask(['path' => $path]);
        $task->run($filePath);

        $changes = $task->getChanges();
        $this->assertCount(1, $changes);

        $changesString = (string)$changes;
        $expected = <<<'TXT'
src/Controller/SomeController.php
-    public $paginate = ['order' => ['Companies.name' => 'ASC']];
+    protected array $paginate = ['order' => ['Companies.name' => 'ASC']];

TXT;
        $this->assertTextEquals($expected, $changesString);
    }
}
