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

/**
 * Task interface for client task classes
 *
 * @author John Pancoast <shideon@gmail.com>
 */
interface TaskInterface {
    public function run();
}