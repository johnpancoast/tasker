<?php
/**
 * Main tasker functionality
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

use Monolog\Logger;

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
     * @var Logger $logger A monolog logger
     *
     * @access protected
     */
    protected $logger;

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
     * @param Logger $logger A monolog logger
     */
    public function __construct(array $tasks, Logger $logger)
    {
        $this->rootDir = __DIR__.'/../';
        $this->setTasks($tasks);
        $this->setLogger($logger);
    }

    /**
     * Run main tasker functionality
     *
     * @access public
     */
    public function run()
    {
        $this->startMicroTime = microtime(true);
        $this->logger->log(Logger::INFO, 'Checking tasks that are due to run.');

        foreach ($this->tasks as $task) {
            if ($task->isDue()) {
                $this->logger->log(Logger::DEBUG, "Task '".$task->getName()."' due to run: YES");

                // run the job in the background.
                // need to allow asynchronous calls for this code
                // to be of any use.
                // TODO add ability to log program output to file
                // although I'm not sure if monolog would be enough there.
                $cmd = "nohup php ".$this->rootDir."console.php shideon:tasker:run_task ".Common::buildTaskCommandArgs($task)." > /dev/null &";

                $this->logger->log(Logger::INFO, "Running task '".$task->getName()."': $cmd");

                shell_exec($cmd);
            } else {
                $this->logger->log(Logger::DEBUG, "Task '".$task->getName()."' due to run: NO");
            }
        }
    }

    /**
     * Set tasks
     *
     * @access public
     * @param array $tasks A collection of {@link Task objects}
     * @return self
     */
    public function setTasks(array $tasks)
    {
        foreach ($tasks as $task) {
            if (!($task instanceof Task)) {
                throw new \Exception('$tasks must be an array of Shideon\Tasker\Task objects.');
            }
        }

        $this->tasks = $tasks;
    }

    /**
     * Set logger
     *
     * @access public
     * @param Logger $logger A monolog logger
     * @return self
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
}