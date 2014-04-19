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
    protected function getConfigOptions()
    {
        return array_merge(
            parent::getConfigOptions(),
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
        try {
            $this->init($input, $output, ['config']);

            $logger = $this->buildLogger();

            $logger->log(Logger::DEBUG, 'Beginning tasker command.');

            $config = Tasker\Common::getConfigArray($this->options['config']);

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

            if ($this->isLogFileRequired()) {
                $tasker->setLogFile($this->options['log_file']);
                $tasker->setLogLevel($this->options['log_level']);
            }

            $output->write($tasker->run());
        } catch (RequiredCommandOptionException $e) {
            // we cannot log these exceptions since we've
            // failed at option validation and we need options
            // for a logger to be created.
            throw $e;
        } catch (\Exception $e) {
            $logger->log(Logger::CRITICAL, 'Encountered exception in '.get_class($this), [$e, 'trace' => $e->getTraceAsString()]);

            throw $e;
        }
    }
}
