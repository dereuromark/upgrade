<?php

namespace Upgrade\Processor;

use Upgrade\Task\ChangeSet;
use Upgrade\Task\FileTaskInterface;

class Processor {

	/**
	 * @var array<class-string<\Upgrade\Task\TaskInterface>>
	 */
	protected array $tasks;

	/**
	 * @var array<string, mixed>
	 */
	protected array $config;

	/**
	 * @param array<class-string<\Upgrade\Task\TaskInterface>> $tasks
	 * @param array<string, mixed> $config
	 */
	public function __construct(array $tasks, array $config) {
		$this->tasks = $tasks;
		$this->config = $config;
	}

	/**
	 * @param string $path
	 *
	 * @return \Upgrade\Task\ChangeSet
	 */
	public function process(string $path): ChangeSet {
		$changeSet = new ChangeSet();

		foreach ($this->tasks as $task) {
			$taskObject = new $task($this->config);

			if ($taskObject instanceof FileTaskInterface) {
				$files = $taskObject->getFiles($path);
				foreach ($files as $file) {
					$taskObject->run($file);

					if (!$taskObject->hasChanges()) {
						continue;
					}
					$changeSet->add($taskObject->getChanges());
				}

				continue;
			}

			$taskObject->run($path);

			if (!$taskObject->hasChanges()) {
				continue;
			}
			$changeSet->add($taskObject->getChanges());
		}

		return $changeSet;
	}

}
