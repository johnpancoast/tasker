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
use Shideon\Tasker as TaskerBase;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

/**
 * Main tasker functionality
 *
 * @author John Pancoast <shideon@gmail.com>
 * @todo add monolog
 */
class Tasker
{
    /**
     * @var array A collection of {@link Task objects}
     *
     * @access private
     */
    private $tasks = [];

    /**
     * @var float The start microtime of a loop of work.
     *
     * @access private
     */
    private $startMicroTime;

    /**
     * @var string The root dir of the project
     *
     * @access private
     * @static
     */
    private static $rootDir;

    /**
     * Constructor
     *
     * @access public
     * @param array $tasks A collection of {@link Task objects}
     */
    public function __construct(array $tasks)
    {
        $this->rootDir = __DIR__.'/../';
        $this->setTasks($tasks);
    }

    /**
     * Run main tasker functionality
     *
     * @access public
     * @param bool $continuous Do we run scheduled jobs regularly?
     * @todo I'm wondering if the continuous functionality should be moved out of here. Not sure yet.
     */
    public function run($continuous = false)
    {
        while (true) {
            $this->startMicroTime = microtime(true);

            foreach ($this->tasks as $task) {
                if ($task->isDue()) {
                    // run the job in the background.
                    // need to allow asynchronous calls for this code
                    // to be of any use.
                    // TODO add ability to log program output to file
                    // although I'm not sure if monolog would be enough there.
                    $cmd = "nohup php ".$this->rootDir."console.php shideon:tasker:run_task ".Common::buildTaskCommandArgs($task)." > /dev/null &";

                    shell_exec($cmd);
                }
            }

            if ($continuous) {
                while (microtime(true) - $this->startMicroTime < 1) {
                    usleep(1000);
                }

                continue;
            }

            return;
        }
    }

    /**
     * Set tasks
     *
     * @access public
     * @param array $tasks A collection of {@link Task objects}
     */
    public function setTasks(array $tasks)
    {
        foreach ($tasks as $task) {
            if (!($task instanceof TaskerBase\Task)) {
                throw new \Exception('$tasks must be an array of Shideon\Tasker\Task objects.');
            }
        }

        $this->tasks = $tasks;
    }
}