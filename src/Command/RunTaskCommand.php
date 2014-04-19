<?php
/**
 * Command to run single task
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker\Command;

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
class RunTaskCommand extends Tasker\Command
{
    /**
     * {@inheritDoc}
     */
    protected function getConfigName()
    {
        return 'shideon:tasker:run_task';
    }

    /**
     * {@inheritDoc}
     */
    protected function getConfigDescription()
    {
        'Run scheduled tasks.';
    }

    /**
     * {@inheritDoc}
     */
    protected function getConfigArguments()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    protected function getConfigOptions()
    {
        return [
            [
                'task_name',
                't',
               InputOption::VALUE_REQUIRED,
                'The name of the task we\'re running.',
                null
            ],
            [
                'task_class',
                'l',
               InputOption::VALUE_REQUIRED,
                'The class to run.',
                null
            ],
            [
                'task_command',
                'm',
               InputOption::VALUE_REQUIRED,
                'The task command to run (if task_class was not passed).',
                null
            ],
            [
                'task_command_arguments',
                'a',
               InputOption::VALUE_REQUIRED,
                'The task command arguments to run.',
                null
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->options = [
                'task_name' => $input->getOption('task_name'),
                'task_class' => $input->getOption('task_class'),
                'task_command' => $input->getOption('task_command'),
                'task_command_arguments' => $input->getOption('task_command_arguments'),
            ];

            Tasker\Task::factory($this->options['task_name'])
                ->setClass($this->options['task_class'])
                ->setCommand($this->options['task_command'])
                ->setCommandArgs((array)json_decode($this->options['task_command_arguments'], true))
                ->run();
        } catch (\Exception $e) {

            // TODO log it

            throw $e;
        }
    }
}
