<?php
/**
 * A single task
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker;

use Shideon\Tasker\TaskInterface;
use Shideon\Tasker\Exception\TaskValidationException;

use Cron\CronExpression;
use Monolog\Logger;

/**
 * A single task
 *
 * @author John Pancoast <shideon@gmail.com>
 */
class Task {
    /**
     * @var string Task name
     *
     * @access private
     */
    private $name;

    /**
     * @var string Task name
     *
     * @access private
     */
    private $time;

    /**
     * @var string Task class
     *
     * @access private
     */
    private $class;

    /**
     * @var string Task command
     *
     * @access private
     */
    private $command;

    /**
     * @var array Task command args
     *
     * @access private
     */
    private $commandArgs = [];

    /**
     * @var Logger $logger A monolog logger
     *
     * @access protected
     */
    protected $logger;

    /**
     * Constructor
     *
     * @access public
     * @param string $name The name of this task.
     * @param string $cronString The cron time string.
     * @param Logger $logger A monolog logger
     */
    public function __construct($name, $cronString = '', Logger $logger)
    {
        $this->setName($name);
        $this->setCronString($cronString);
        $this->setLogger($logger);
    }

    /**
     * Simple static factory for convenience
     *
     * @access public
     * @static
     * @param string $name The name of this task.
     * @param string $cronString The cron time string.
     * @param Logger $logger A monolog logger
     * @return self
     */
    public static function factory($name, $cronString = '', Logger $logger)
    {
        return new self($name, $cronString, $logger);
    }

    /**
     * Run task
     *
     * Note that the optional params are here because the lib expects this
     * functionality but if a developer were to extend the lib, these may
     * no longer be relevant. For more on this, see the comment that's inside
     * {@link Shideon\Tasker\AbstractCommand::getConfigOptions()}
     *
     * @access public
     * @param bool $async Run asynchronously
     * @param string $configFile Config file to send to command.
     * @param string $logFile Log file to send to command.
     * @param string $logLevel Log level to pass to command.
     */
    public function run($async, $configFile = null, $logFile = null, $logLevel = null)
    {
        $this->validate();

        $cmd = $this->buildRunCommand($async, $configFile, $logFile, $logLevel);

        $this->logger->log(Logger::INFO, "Running task '".$this->getName()."' in background: $cmd");

        // note that we use shell_exec instead of symfony's
        // Process because it's causing issues with
        // sub process having open file handle.
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
     * @param bool $includeAsync Include parts of the command for asynchronousity.
     * @param string $configFile Config file to send to command.
     * @param string $logFile Log file to send to command.
     * @param string $logLevel Log level to pass to command.
     */
    public function buildRunCommand($includeAsync = true, $configFile = null, $logFile = null, $logLevel = null)
    {
        $this->validate();

        $cmd = '';

        if ($includeAsync) {
            $cmd .= 'nohup';
        }

        if ($this->getClass()) {
            $cmd .= " bin/console shideon:tasker:run_task --task_name='".$this->getName()."'";

            if ($configFile) {
                $cmd .= " --config_file='$configFile'";
            }
            if ($logFile) {
                $cmd .= " --log_file='$logFile'";
            }
            if ($logLevel) {
                $cmd .= " --log_level='$logLevel'";
            }
        } elseif ($this->getCommand()) {
            $c = $this->getCommand();

            if (strpos($c, '$console') !== false) {
                $cmd .= ' '.str_replace('$console', $_SERVER['argv'][0], $c);

                if ($configFile) {
                    $cmd .= " --config_file='$configFile'";
                }
                if ($logFile) {
                    $cmd .= " --log_file='$logFile'";
                }
                if ($logLevel) {
                    $cmd .= " --log_level='$logLevel'";
                }
            } else {
                $cmd .= ' '.$c;
            }
        }

        if ($includeAsync) {
            $cmd .= ' > /dev/null &';
        }

        return $cmd;
    }


    /**
     * Run task from class
     *
     * @access public
     */
    public function callClass()
    {
        $this->validate();

        if ($this->file) {
            if (!is_readable($this->file)) {
                throw new \Exception('Task class file not readable: '.$this->file);
            }

            require $this->file;
        }

        if (!class_exists($this->class)) {
            throw new \Exception('Task class '.$this->class.' does not exist.');
        }

        $obj = new $this->class;

        if (!($obj instanceof TaskInterface)) {
            throw new \Exception('Task must implement Shideon\Tasker\TaskInterface');
        } 

        $obj->run($this, $this->logger);
    }

    /**
     * Are we do to run
     *
     * @access public
     * @return bool
     */
    public function isDue()
    {
        $this->validate();

        return CronExpression::factory($this->getCronString())->isDue();
    }

    /**
     * Validate object
     *
     * @access public
     * @throws TaskValidationException if not valid.
     */
    public function validate()
    {
        if (!$this->class && !$this->command) {
            throw new TaskValidationException('Must set class or command');
        }

        if ($this->class && $this->command) {
            throw new TaskValidationException('Cannot set class and command since we won\'t know which to run');
        }
    }

    /**
     * Set object params from array
     *
     * @access public
     * @param array $task Array to set class param from.
     * @return self
     */
    public function setFromArray(array $task)
    {
        if (isset($task['name'])) {
            $this->setName($task['name']);
        }
        if (isset($task['time'])) {
            $this->setCronString($task['time']);
        }
        if (isset($task['class'])) {
            $this->setClass($task['class']);
        }
        if (isset($task['file'])) {
            $this->setFile($task['file']);
        }

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return string Task name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the value of name.
     *
     * @param string Task name $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of cron time string.
     *
     * @return Cron time string.
     */
    public function getCronString()
    {
        return $this->cronString;
    }
    
    /**
     * Sets the value of cron time string
     *
     * @param string $cronString The cron time string.
     *
     * @return self
     */
    public function setCronString($cronString)
    {
        $this->cronString = $cronString;

        return $this;
    }

    /**
     * Gets the value of file.
     *
     * @return string Task file
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * Sets the value of file.
     *
     * @param string $file Set the file
     *
     * @return self
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }


    /**
     * Gets the value of class.
     *
     * @return string Task class
     */
    public function getClass()
    {
        return $this->class;
    }
    
    /**
     * Sets the value of class.
     *
     * @param string Task class $class the class
     *
     * @return self
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Gets the value of command.
     *
     * @return string Task command
     */
    public function getCommand()
    {
        return $this->command;
    }
    
    /**
     * Sets the value of command.
     *
     * @param string $command Set the command
     *
     * @return self
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Gets the value of commandArgs.
     *
     * @return array Task command args
     */
    public function getCommandArgs()
    {
        return $this->commandArgs;
    }
    
    /**
     * Sets the value of commandArgs.
     *
     * @param array $commandArgs Task command args 
     *
     * @return self
     */
    public function setCommandArgs(array $commandArgs)
    {
        $this->commandArgs = $commandArgs;

        return $this;
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