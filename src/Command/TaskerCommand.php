<?php
/**
 * Command to execute scheduled jobs either once or continuously as a daemon.
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
 * Command to execute scheduled jobs once or continuously as a daemon.
 *
 * @author John Pancoast <shideon@gmail.com>
 */
class TaskerCommand extends Tasker\AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function getConfigName()
    {
        return 'shideon:tasker';
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
                    'config',
                    'c',
                   InputOption::VALUE_REQUIRED,
                    'Config file.',
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
        $this->init($input, $output, ['config']);

        $config = Tasker\Common::getConfigArray($this->options['config']);

        foreach ($config['tasker']['tasks'] as $task)
        {
            $taskObj = new Tasker\Task($task['name'], $task['time']);

            if (isset($task['class'])) {
                $taskObj->setClass($task['class']);
            }

            if (isset($task['command'])) {
                $taskObj->setCommand($task['command']);
                $taskObj->setArgument($task['command_args']);
            }

            $tasks[] = $taskObj;
        }

        $tasker = new Tasker\Tasker($tasks, $this->buildLogger());
        $output->write($tasker->run());
    }
}
