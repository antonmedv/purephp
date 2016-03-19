<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Console;

use Pure\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ClientCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('client')
            ->setDescription('Console client for PurePHP server')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Port number', 1337)
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host address', '127.0.0.1');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Welcome to Console Client for PurePHP server.</info>');

        $pure = new Client($input->getOption('port'), $input->getOption('host'));

        $language = new ExpressionLanguage();

        $auto = [
            'pure',
            'pure.map',
            'pure.queue',
            'pure.stack',
            'pure.priority',
            'pure.delete',
            'pure.ping',
            'exit',
        ];

        $helper = $this->getHelper('question');
        $question = new Question('> ', '');
        
        do {
            $question->setAutocompleterValues($auto);
            $command = $helper->ask($input, $output, $question);
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
                $output->writeln('<error>' . get_class($e) . ": \n" . $e->getMessage() . '</error>');
            }

        } while (true);
    }
}
