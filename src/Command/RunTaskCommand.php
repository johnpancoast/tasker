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
class RunTaskCommand extends Tasker\AbstractCommand
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
        return 'Run scheduled tasks.';
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
        return array_merge(
            parent::getConfigOptions(true),
            [
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
                ],
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function isLogFileRequired()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->init($input, $output, ['log_file', 'task_class', 'task_name']);

            Tasker\Task::Factory($this->options['task_name'], $this->buildLogger())
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
