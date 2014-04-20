<?php
/**
 * Command to run single task
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker\Command;

use Shideon\Tasker;
use Shideon\Tasker\Exception\RequiredCommandOptionException;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Monolog\Logger;

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
            parent::getConfigOptions(),
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
    protected function getRequiredOptions()
    {
        return array_merge(parent::getRequiredOptions(), ['task_class', 'task_name']);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->init($input, $output);
            $logger = $this->buildLogger();

            $logger->log(Logger::DEBUG, "Running task '".$this->options['task_name']."'. Calling Shideon\Tasker\Task.");

            Tasker\Task::Factory($this->options['task_name'], '', $logger)
                ->setClass($this->options['task_class'])
                ->setCommand($this->options['task_command'])
                ->setCommandArgs((array)json_decode($this->options['task_command_arguments'], true))
                ->run();
        } catch (RequiredCommandOptionException $e) {
            // we cannot log these exceptions since we've
            // failed at option validation and we need options
            // for a logger to be created.
            throw $e;
        } catch (\Exception $e) {
            $logger->log(Logger::CRITICAL, "Running task '".$this->options['task_name']."' encountered exception in ".get_class($this), [$e, 'trace' => $e->getTraceAsString()]);

            throw $e;
        }
    }
}
