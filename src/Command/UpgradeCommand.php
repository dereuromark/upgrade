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
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         4.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Upgrade\Command;

use Cake\Console\Arguments;
use Cake\Console\BaseCommand;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Entry point into the upgrade process
 */
class UpgradeCommand extends BaseCommand
{
    /**
     * Execute.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $path = rtrim((string)$args->getArgument('path'), DIRECTORY_SEPARATOR);
        $path = realpath($path);

        $io->out('Upgrading skeleton. You can diff and revert anything that needs to stay after the operation!');
        $continue = $io->askChoice('Make sure you commited/backup up your files. Continue?', ['y', 'n'], 'n');
        if ($continue !== 'y') {
            $io->abort('Aborted');
        }

        $this->skeletonUpgrade($args);

        $io->warning('Now check the changes via diff in your IDE and revert the lines you want to keep.');

        return static::CODE_SUCCESS;
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to build
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription([
                '<question>Upgrade tool for CakePHP 4.0</question>',
                '',
                'Runs all of the sub commands on an application/plugin. The <info>path</info> ' .
                'argument should be the application or plugin root directory.',
                '',
                'You can also run each command individually on specific directories if you want more control.',
                '',
                '<info>Sub-Commands</info>',
                '',
                '- file_rename  Rename template and locale files',
                '- rector       Apply rector rules for phpunit80 and cakephp40',
            ])
            ->addArgument('path', [
                'help' => 'The path to the application or plugin.',
                'required' => true,
            ])
            ->addOption('dry-run', [
                'help' => 'Dry run.',
                'boolean' => true,
            ]);

        return $parser;
    }

    /**
     * @param \Cake\Console\Arguments $args
     *
     * @return void
     */
    protected function skeletonUpgrade(Arguments $args): void
    {

    }
}
