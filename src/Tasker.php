<?php
/**
 * Main tasker functionality
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker;

use Symfony\Component\Console\Output\OutputInterface;
use Shideon\Tasker;

/**
 * Main tasker functionality
 *
 * @author John Pancoast <shideon@gmail.com>
 */
class Tasker {
    private $tasks = [];

    public function __construct(array $tasks)
    {
        $this->tasks = $tasks;
    }

    public function run($continuos = false)
    {
        while (true) {
            $startMicro = microtime(true);

            foreach ($this->tasks as $task)
            {
                $taskObj = new Tasker\Task;
                $taskObj->setName($task['name']);
                $taskObj->setTime($task['time']);

                if (isset($task['class'])) {
                    $taskObj->setClass($task['class']);
                }

                if (isset($task['command'])) {
                    $taskObj->setCommand($task['command']);
                    $taskObj->setArgument($task['command_args']);
                }

                $taskObj->run();
            }

            if ($continuous) {
                usleep(min(0, (float)1000000 - (microtime(true) - $startMicro)));
                continue;
            }

            return;
        }
    }
}