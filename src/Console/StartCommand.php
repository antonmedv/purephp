<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Console;

use Pure\Storage\ArrayStorage;
use Pure\Storage\QueueStorage;
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
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host address', '127.0.0.1')
            ->addOption('fixture', 'f', InputOption::VALUE_OPTIONAL, 'Load fixtures', false);
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

        if ($input->getOption('fixture')) {
            $as = new ArrayStorage($server);
            $as->push(['a' => 1]);
            $as->push(['b' => 2]);
            $as->push(['c' => 3]);
            $as->push(['d' => 4]);
            $as->push(['e' => 5]);
            $qs = new QueueStorage($server);
            $qs->push(['a' => 1]);
            $qs->push(['b' => 2]);
            $qs->push(['c' => 3]);
            $qs->push(['d' => 4]);
            $qs->push(['e' => 5]);
            $server->setStores([
                ArrayStorage::class => [
                    'a' => $as,
                ],
                QueueStorage::class => [
                    'a' => $qs,
                ],
            ]);
        }

        $server->run();
    }
} 