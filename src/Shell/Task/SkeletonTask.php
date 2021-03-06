<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link http://cakephp.org CakePHP(tm) Project
 * @since 3.0.0
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Cake\Upgrade\Shell\Task;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;

/**
 * Create and setup missing files and folders via app repo.
 *
 * @property \Cake\Upgrade\Shell\Task\StageTask $Stage
 */
class SkeletonTask extends BaseTask {

	use ChangeTrait;

	/**
	 * @var array
	 */
	public $tasks = ['Stage'];

	/**
	 * Add missing files and folders in the root app dir.
	 *
	 * @param string $path
	 * @return bool
	 */
	protected function _process($path) {
		$path = dirname($path) . DS;

		$dirs = ['logs', 'bin', 'config', 'webroot', 'tests'];
		foreach ($dirs as $dir) {
			if (!is_dir($path . $dir) && empty($this->params['dry-run'])) {
				mkdir($path . DS . $dir, 0770, true);
			}
		}

		if (!is_file($path . 'logs' . DS . 'empty') && empty($this->params['dry-run'])) {
			touch($path . 'logs' . DS . 'empty');
		}

		$sourcePath = ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'app' . DS;
		$this->prepareSkeletonAppCode($sourcePath);

		$files = [
			'bin' . DS . 'cake',
			'bin' . DS . 'cake.bat',
			'bin' . DS . 'cake.php',
			'phpunit.xml.dist',
			'index.php',
			'webroot' . DS . 'index.php',
			'webroot' . DS . 'css' . DS . 'cake.css',
			'webroot' . DS . 'css' . DS . 'home.css',
			'webroot' . DS . 'css' . DS . 'milligram.min.css',
			'webroot' . DS . 'css' . DS . 'normalize.min.css',
			'config' . DS . 'bootstrap.php',
			'config' . DS . 'bootstrap_cli.php',
			'config' . DS . 'paths.php',
			'config' . DS . 'requirements.php',
			'config' . DS . 'routes.php',
			'tests' . DS . 'bootstrap.php',
			'src' . DS . 'Application.php',
			'src' . DS . 'View' . DS . 'AppView.php',
			'src' . DS . 'View' . DS . 'AjaxView.php',
			'src' . DS . 'Controller' . DS . 'PagesController.php',
			'templates' . DS . 'Error' . DS . 'error400.php',
			'templates' . DS . 'Error' . DS . 'error500.php',
			'templates' . DS . 'layout' . DS . 'error.php',
			'templates' . DS . 'element' . DS . 'flash' . DS . 'default.php',
			'templates' . DS . 'element' . DS . 'flash' . DS . 'error.php',
			'templates' . DS . 'element' . DS . 'flash' . DS . 'success.php',
		];
		$ret = 0;
		foreach ($files as $file) {
			$ret |= $this->_addFile($file, $sourcePath, $path);
		}
		$ret |= $this->_addFile('config' . DS . 'app.php', $sourcePath, $path, 'config' . DS . 'app.php');

		return (bool)$ret;
	}

	/**
	 * _addFile()
	 *
	 * @param string $file
	 * @param string $sourcePath
	 * @param string $targetPath
	 * @param string|null $targetFile
	 * @return bool
	 */
	protected function _addFile($file, $sourcePath, $targetPath, $targetFile = null) {
		$result = false;
		if (!is_file($targetPath . $file) || !empty($this->params['overwrite'])) {
			$result = true;
			if (empty($this->params['dry-run'])) {
				if ($targetFile === null) {
					$targetFile = $file;
				}
				$targetPathName = $targetPath . dirname($targetFile);
				if (!is_dir($targetPathName)) {
					mkdir($targetPathName, 0755, true);
				}
				$result = copy($sourcePath . $file, $targetPath . $targetFile);
			}
			$this->out('Adding ' . $file, 1, Shell::VERBOSE);
		}

		return $result;
	}

	/**
	 * _shouldProcess
	 *
	 * Is the current path within the scope of this task?
	 *
	 * @param string $path
	 * @return bool
	 */
	protected function _shouldProcess($path) {
		if (basename($path) === 'composer.json' && empty($this->params['plugin'])) {
			return true;
		}

		return false;
	}

	/**
	 * Get the option parser for this shell.
	 *
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser(): ConsoleOptionParser {
		return parent::getOptionParser()
			->addOptions([
				'overwrite' => [
					'short' => 'o',
					'boolean' => true,
					'help' => 'Overwrite files even if they already exist.',
				],
			]);
	}

	/**
	 * @param string $sourcePath
	 *
	 * @return void
	 */
	protected function prepareSkeletonAppCode(string $sourcePath): void {
		if (!is_dir($sourcePath)) {
			$parentPath = dirname($sourcePath);
			if (!is_dir($parentPath)) {
				mkdir($parentPath, 0770, true);
			}
			exec('cd ' . $parentPath . ' && git clone https://github.com/cakephp/app.git');
		}

		exec('cd ' . $sourcePath . ' && git pull');
	}

}
