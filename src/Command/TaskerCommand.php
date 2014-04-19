<?php
/**
 * Command to execute scheduled jobs either once or continuously as a daemon.
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Shideon\Tasker;

/**
 * Command to execute scheduled jobs once or continuously as a daemon.
 *
 * @author John Pancoast <shideon@gmail.com>
 * @todo add monolog
 */
class TaskerCommand extends ConsoleCommand
{
    /**
     * @var array Passed args and options
     *
     * @access private
     */
    private $options = [];

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('shideon:tasker')
            ->setDescription('Run scheduled tasks.')
            ->addOption(
                'config',
                'c',
               InputOption::VALUE_REQUIRED,
                'Config file.'
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->options = [
            'config' => $input->getOption('config'),
        ];

        if (!$this->options['config']) {
            throw new \Exception('Must set --config.');
        }

        // absolute or relative path can be passed.
        $file = (substr($this->options['config'], 0, 1) == '/') ? $this->options['config'] : getcwd().'/'.$this->options['config'];

        $tasker = new Tasker\Tasker($file);
        $output->write($tasker->run());
    }
}
