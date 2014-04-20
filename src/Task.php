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
     * @access public
     */
    public function run()
    {
        $this->validate();

        if ($this->class) {
            $this->runClass();
        } elseif ($this->command) {
            $this->runCommand();
        }
    }

    /**
     * Run task from class
     *
     * @access private
     * @return 
     */
    private function runClass()
    {
        if (!class_exists($this->class)) {
            throw new \Exception('Task class '.$this->class.' does not exist.');
        }

        $obj = new $this->class;

        if (!($obj instanceof TaskInterface)) {
            throw new \Exception('Task must implement Shideon\Tasker\TaskInterface');
        } 

        $obj->run();
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