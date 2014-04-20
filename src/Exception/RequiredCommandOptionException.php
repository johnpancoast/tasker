<?php
/**
 * Required command option exception
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker\Exception;

/**
 * Task validation exception
 *
 * @author John Pancoast <shideon@gmail.com>
 */
class RequiredCommandOptionException extends \Exception
{
    /**
     * @var string The key that failed
     *
     * @access private
     */
    private $key;

    /**
     * Override parent constructor.
     *
     * Note that we allow a string for our second param for the key that failed
     * but we call the parent without the second param which is
     * (long)code in exception)
     *
     * @access public
     * {@inheritDoc}
     * @param string $message The exception message
     * @param string $key The key that failed.
     */
    public function __construct($message, $key)
    {
        $this->setKey($key);
        parent::__construct($message);
    }

    /**
     * Set the key that failed
     *
     * @access public
     * @param string $key The key that failed.
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the key that failed
     *
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
