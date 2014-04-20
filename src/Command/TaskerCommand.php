<?php
/**
 * Command to execute scheduled jobs either once or continuously as a daemon.
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->init($input, $output);

            $logger = $this->buildLogger();
            $logger->log(Logger::DEBUG, 'Beginning tasker command.');

            $config = Tasker\Common::getConfigArray($this->options['config_file']);

            foreach ($config['tasker']['tasks'] as $task)
            {
                $taskObj = new Tasker\Task($task['name'], $task['time'], $logger);

                if (isset($task['class'])) {
                    $taskObj->setClass($task['class']);
                }

                if (isset($task['command'])) {
                    $taskObj->setCommand($task['command']);
                    $taskObj->setArgument($task['command_args']);
                }

                $tasks[] = $taskObj;
            }

            $logger->log(Logger::DEBUG, 'Calling on Shideon\Tasker\Tasker');

            $tasker = new Tasker\Tasker($tasks, $logger);

            // for passing to tasker->run(). our lib requires these values
            // but extenders may not need them so they're optional
            // and this is determined by their existence in the command
            // options.
            $configFile = null;
            $logFile = null;
            $logLevel = null;

            $requiredOptions = $this->getRequiredOptions();

            if (array_search('config_file', $requiredOptions) !== false) {
                $configFile = $this->options['config_file'];
            }

            if (array_search('log_file', $requiredOptions) !== false) {
                $logFile = $this->options['log_file'];
                $logLevel = $this->options['log_level'];
            }

            $output->write($tasker->run($configFile, $logFile, $logLevel));
        } catch (\Exception $e) {
            // if we don't have a logger (due to exception being thrown before
            // it's instantiatied) then attempt to build one. ignore its
            // exceptions.
            if (!isset($logger) || !($logger instanceof Logger)) {
                try {
                    $logger = $this->buildLogger();
                } catch (\Exception $e) {
                    // ignore
                }
            }

            if (isset($logger) && $logger instanceof Logger) {
                $logger->log(Logger::CRITICAL, "Running task '".$this->options['task_name']."' encountered exception in ".get_class($this), [$e, 'trace' => $e->getTraceAsString()]);
            }

            throw $e;
        }
    }
}
