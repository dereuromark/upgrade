<?php

namespace Upgrade\Task;

interface TaskInterface {

	/**
	 * @param string $path
	 *
	 * @return void
	 */
	public function run(string $path): void;

	/**
	 * @return bool
	 */
	public function hasChanges(): bool;

	/**
	 * @return \Upgrade\Task\ChangeSet
	 */
	public function getChanges(): ChangeSet;

}
