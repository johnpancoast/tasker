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
use Symfony\Component\Process\Process;

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
     * Get tasks that are due to run
     *
     * @access public
     * @return array A list of {@link Task} objects.
     */
    public function getDueTasks()
    {
        $this->logger->log(Logger::DEBUG, 'Checking tasks that are due to run. '.count($this->tasks).' total tasks.');

        $t = [];

        foreach ($this->tasks as $task) {
            $this->logger->log(Logger::DEBUG, 'Checking on "'.$task->getName().'" ('.$task->getCronString().')');

            if ($task->isDue()) {
                $t[] = $task;

                $this->logger->log(Logger::DEBUG, '"'.$task->getName().'" IS set to run');
            } else {
                $this->logger->log(Logger::DEBUG, '"'.$task->getName().'" is not set to run');
            }
        }

        return $t;
    }

    /**
     * Run main tasker functionality
     *
     * Note that the optional params are here because the lib expects this
     * functionality but if a developer were to extend the lib, these may
     * no longer be relevant. For more on this, see the comment that's inside
     * {@link Shideon\Tasker\AbstractCommand::getConfigOptions()}
     *
     * @access public
     * @param string $configFile Config file to send to command.
     * @param string $logFile Log file to send to command.
     * @param string $logLevel Log level to pass to command.
     * @uses self::getDueTasks()
     * @uses self::callRunTask()
     */
    public function run($configFile = null, $logFile = null, $logLevel = null)
    {
        $this->startMicroTime = microtime(true);

        foreach ($this->getDueTasks() as $task) {
            $this->callRunTask($task, true, $configFile, $logFile, $logLevel);
        }
    }

    /**
     * Call the run task command.
     *
     * Note that the optional params are here because the lib expects this
     * functionality but if a developer were to extend the lib, these may
     * no longer be relevant. For more on this, see the comment that's inside
     * {@link Shideon\Tasker\AbstractCommand::getConfigOptions()}
     *
     * @access public
     * @param Task $task Task object
     * @param bool $async Run asynchronously
     * @param string $configFile Config file to send to command.
     * @param string $logFile Log file to send to command.
     * @param string $logLevel Log level to pass to command.
     */
    public function callRunTask(Task $task, $async = true, $configFile = null, $logFile = null, $logLevel = null)
    {
        $cmd = $this->buildRunTaskCommand($task, $async, $configFile, $logFile, $logLevel);

        $this->logger->log(Logger::INFO, "Running task '".$task->getName()."' in background: $cmd");

        // note that we use shell_exec instead of symfony's
        // Process because it's causing issues with
        // sub process having open file hdnle.
        shell_exec($cmd);
    }

    /**
     * Generate command for running a task
     *
     * Note that the optional params are here because the lib expects this
     * functionality but if a developer were to extend the lib, these may
     * no longer be relevant. For more on this, see the comment that's inside
     * {@link Shideon\Tasker\AbstractCommand::getConfigOptions()}
     *
     * @access public
     * @param Task $task Task object
     * @param bool $includeAsync Include parts of the command for asynchronousity.
     * @param string $configFile Config file to send to command.
     * @param string $logFile Log file to send to command.
     * @param string $logLevel Log level to pass to command.
     */
    public function buildRunTaskCommand(Task $task, $includeAsync = true, $configFile = null, $logFile = null, $logLevel = null)
    {
        $cmd = '';

        if ($includeAsync) {
            $cmd .= 'nohup';
        }

        $cmd .= " php ".$this->rootDir."console.php shideon:tasker:run_task ".$this->buildRunTaskCommandArgs($task);

        if ($configFile) {
            $cmd .= " --config_file='$configFile'";
        }
        if ($logFile) {
            $cmd .= " --log_file='$logFile'";
        }
        if ($logLevel) {
            $cmd .= " --log_level='$logLevel'";
        }

        if ($includeAsync) {
            $cmd .= ' > /dev/null &';
        }

        return $cmd;
    }

    /**
     * Given a {@link Task} object, make command arguments for the run task command.
     *
     * @access public
     * @static
     * @param Task
     * @return string
     */
    public static function buildRunTaskCommandArgs(Task $task)
    {
        $cmd = "--task_name='".$task->getName()."'";

        return $cmd;
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
