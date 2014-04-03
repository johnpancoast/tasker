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
class Tasker {
    /**
     * @var string The config file
     *
     * @qaccess private
     */
    private $configFile;

    /**
     * @var array A collection of {@link Task objects}
     *
     * @access private
     */
    private $tasks = [];

    /**
     * @var bool Has the class initialized
     *
     * @access private
     */
    private $isInit = false;

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
     */
    public function __construct($configFile)
    {
        $this->configFile = $configFile;
        $this->rootDir = __DIR__.'/../';
    }

    /**
     * Ininitizlie
     *
     * @access private
     */
    private function init()
    {
        if ($this->isInit) {
            return;
        }

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration(
            $configuration,
            [Yaml::parse($this->configFile)]
        );

        foreach ($config['tasker'] as $task)
        {
            $taskObj = new TaskerBase\Task;
            $taskObj->setName($task['name']);
            $taskObj->setTime($task['time']);

            if (isset($task['class'])) {
                $taskObj->setClass($task['class']);
            }

            if (isset($task['command'])) {
                $taskObj->setCommand($task['command']);
                $taskObj->setArgument($task['command_args']);
            }

            $this->tasks[$task['name']] = $taskObj;
        }
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

            $this->init();

            foreach ($this->tasks as $task) {
                if ($task->doRun()) {
                    // run the job in the background.
                    // need to allow asynchronous calls for this code
                    // to be of any use.
                    // TODO add ability to log program output to file
                    // although I'm not sure if monolog would be enough there.
                    $cmd = "nohup php ".self::$rootDir."console.php shideon:tasker:run_task --config='".$this->configFile."' --task_name='".$task->getName()."' > /dev/null";
                    $output = shell_exec($cmd);

                    // TODO check output for errors, log.

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
}