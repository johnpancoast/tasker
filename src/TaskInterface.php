<?php
/**
 * Task interface for client task classes
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker;

use Shideon\Tasker\Exception\TaskValidationException;
use Monolog\Logger;

/**
 * Task interface for client task classes
 *
 * @author John Pancoast <shideon@gmail.com>
 */
interface TaskInterface {
    /**
     * Functionality for your class.
     *
     * @access public
     * @param Logger $logger A monolog logger.
     */
    public function run(Logger $logger);
}