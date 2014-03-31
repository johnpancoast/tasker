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
     * Run task
     *
     * @access public
     */
    public function run()
    {
        if (!$this->doRun()) {
            return;
        }

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
     */
    private function runClass()
    {
        $obj = new $this->class;

        if (!($obj instanceof TaskInterface)) {
            throw new \Exception('Task must implement Shideon\Tasker\TaskInterface');
        } 
    }

    /**
     * Should we run?
     *
     * @access public
     * @return bool
     */
    public function doRun()
    {
        $this->validate();

        // check time string, return bool

    }

    /**
     * Validate object
     *
     * @access public
     * @throws TaskValidationException if not valid.
     */
    public function validate()
    {
        if (!$this->name) {
            throw new TaskValidationException('Must set name before running');
        }

        if (!$this->time) {
            throw new TaskValidationException('Must set time before running');
        }

        if (!$this->class && !$this->command) {
            throw new TaskValidationException('Must set class or command');
        }

        if ($this->class && $this->command) {
            throw new TaskValidationException('Cannot set class and command since we won\'t know which to run');
        }

        if ($this->command && empty($this->commandArgs)) {
            throw new TaskValidationException('Must set command args');
        }
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
     * Gets the value of time.
     *
     * @return string Task name
     */
    public function getTime()
    {
        return $this->time;
    }
    
    /**
     * Sets the value of time.
     *
     * @param string Task name $time the time
     *
     * @return self
     */
    public function setTime($time)
    {
        $this->time = $time;

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
}