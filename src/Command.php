<?php
/**
 * Parent command code.
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Parent command code.
 *
 * @author John Pancoast <shideon@gmail.com>
 * @todo add monolog
 */
class Command extends ConsoleCommand
{
    /**
     * @var array Passed args and options
     *
     * @access protected
     */
    protected $options = [];
}
