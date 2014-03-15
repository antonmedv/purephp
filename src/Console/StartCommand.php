<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('start')
            ->setDescription('Start PurePHP server')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Port number', 1337)
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host address', '127.0.0.1');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($output->isVerbose()) {
            $output->writeln('<info>PurePHP server started.</info>');
        }

        $server = new \Pure\Server($input->getOption('port'), $input->getOption('host'));

        if ($output->isDebug()) {
            $server->setLogger(function ($log) use ($output) {
                $output->writeln($log);
            });
        }

        $server->run();
    }
} 