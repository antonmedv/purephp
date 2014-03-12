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
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class ClientCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('client')
            ->setDescription('Console client for PurePHP server')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Port number', 1337)
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host address', '127.0.0.1');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Welcome to Console Client for PurePHP server.</info>');

        $pure = new \Pure\Client($input->getOption('port'), $input->getOption('host'));

        $language = new ExpressionLanguage();


        $auto = [
            'pure',
            'pure.of',
            'pure.lifetime',
            'pure.queue',
            'pure.stack',
            'pure.priority',
            'exit',
        ];

        $dialog = $this->getHelperSet()->get('dialog');

        do {
            $command = $dialog->ask(
                $output,
                '> ',
                '',
                $auto
            );
            $auto[] = $command;


            if ('exit' === $command) {
                break;
            }

            try {
                $result = $language->evaluate($command, [
                    'pure' => $pure,
                ]);

                var_dump($result);

            } catch (\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
            }

        } while (true);
    }
} 