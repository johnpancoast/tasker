<?php
/**
 * Command to run single task
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
 * Command to run single task
 *
 * @author John Pancoast <shideon@gmail.com>
 * @todo add monolog
 */
class RunTaskCommand extends ConsoleCommand
{
    /**
     * @var array Passed args and options
     *
     * @access private
     */
    private $options = [];

    /**
     * @var array Processed configuration
     *
     * @access private
     */
    private $config = [];

    protected function configure()
    {
        $this
            ->setName('shideon:tasker:run_task')
            ->setDescription('Run scheduled tasks.')
            ->addOption(
                'config',
                'c',
               InputOption::VALUE_REQUIRED,
                'Config file.'
            )
            ->addOption(
                'task_name',
                't',
               InputOption::VALUE_REQUIRED,
                'The task name to run.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->options = [
            'config' => $input->getOption('config'),
            'task_name' => $input->getOption('task_name')
        ];

        // just debug it for now.
        file_put_contents(__DIR__.'/../../pancoast/log', print_r($this->options, true));
    }
}
