<?php
/**
 * Command to run single task
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker;

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
            ->setName('shideon:tasker:task')
            ->setDescription('Run scheduled tasks.')
            ->addOption(
                'object',
                'o',
               InputOption::VALUE_REQUIRED,
                'Serialized task object that must be of type Shideon\Tasker\Task.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

        $this->options = [
            'object' => $input->getOption('object'),
        ];

        $task2 = unserialize('O:19:"Shideon\Tasker\Task":5:{s:25:"Shideon\Tasker\Taskname";s:5:"A job";s:25:"Shideon\Tasker\Tasktime";s:11:"* * * * * *";s:26:"Shideon\Tasker\Taskclass";s:3:"meh";s:28:"Shideon\Tasker\Taskcommand";N;s:32:"Shideon\Tasker\TaskcommandArgs";a:0:{}}');

        print_r($task2);exit;

        $task = unserialize($this->options['object']);
        echo "\n\n".$this->options['object']."\n\n";
        echo "TASK: "; print_r($task);
        exit;
        file_put_contents(__DIR__."/../pancoast/log", $task->getName(), FILE_APPEND);
    }
}
