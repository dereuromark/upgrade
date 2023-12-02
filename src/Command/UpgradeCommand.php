<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link https://cakephp.org CakePHP(tm) Project
 * @since 4.0.0
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Upgrade\Command;

use Cake\Console\Arguments;
use Cake\Console\BaseCommand;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Entry point into the upgrade process
 */
class UpgradeCommand extends BaseCommand {

	/**
	 * @var \Cake\Console\Arguments
	 */
	protected Arguments $args;

	/**
	 * @var \Cake\Console\ConsoleIo
	 */
	protected ConsoleIo $io;

	/**
	 * Execute.
	 *
	 * @param \Cake\Console\Arguments $args The command arguments.
	 * @param \Cake\Console\ConsoleIo $io The console io
	 * @return int|null
	 */
	public function execute(Arguments $args, ConsoleIo $io): ?int {
		$this->args = $args;
		$this->io = $io;

		$path = rtrim((string)$args->getArgument('path'), DIRECTORY_SEPARATOR);
		$path = realpath($path);
		if ($path === false) {
			$io->abort('Path cannot be read. Maybe a typo?');
		}

		$io->out('Upgrading skeleton. You can diff and revert anything that needs to stay after the operation!');
		$continue = $io->askChoice('Make sure you commited/backup up your files. Continue?', ['y', 'n'], 'n');
		if ($continue !== 'y') {
			$io->abort('Aborted');
		}

		$result = $this->skeletonUpgrade($path);
		if (!$result) {
			$io->error('Could not fully process the upgrade task');
		}

		$io->warning('Now check the changes via diff in your IDE and revert the lines you want to keep.');

		return static::CODE_SUCCESS;
	}

	/**
	 * Gets the option parser instance and configures it.
	 *
	 * @param \Cake\Console\ConsoleOptionParser $parser The parser to build
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
		$parser
			->setDescription([
				'<question>Upgrade tool addon for CakePHP 5.x</question>',
				'',
				'<info>Runs the following tasks:</info>',
				'',
				'- skeleton',
			])
			->addArgument('path', [
				'help' => 'The path to the application or plugin.',
				'required' => true,
			])
			->addOption('overwrite', [
				'help' => 'Overwrite.',
				'boolean' => true,
				'short' => 'o',
			])
			->addOption('dry-run', [
				'help' => 'Dry run.',
				'boolean' => true,
				'short' => 'd',
			]);

		return $parser;
	}

	/**
	 * @param string $path
	 *
	 * @return bool
	 */
	protected function skeletonUpgrade(string $path): bool {
		$sourcePath = ROOT . DS . 'tmp' . DS . 'app' . DS;
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

		if (!file_exists($sourcePath . $file)) {
			$this->io->info('Source file ' . $file . 'cannot be found, skipping.');

			return false;
		}

		$fileExists = file_exists($targetPath . $file);
		if (!$fileExists || $this->args->getOption('overwrite')) {
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
			$this->io->verbose(($fileExists ? 'Replacing' : 'Adding') . ' ' . $file);
		}

		return $result;
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
