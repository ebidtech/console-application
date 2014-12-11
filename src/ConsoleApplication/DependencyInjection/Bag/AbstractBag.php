<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\DependencyInjection\Bag;

use ConsoleApplication\DependencyInjection\Container;
use ConsoleApplication\Exception\ResourceNotFoundException;

abstract class AbstractBag implements BagInterface
{
    /*------------------------------------------------------------------------*\
    | Attributes                                                               |
    \*------------------------------------------------------------------------*/

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $keys;

    /*------------------------------------------------------------------------*\
    | Constructor                                                              |
    \*------------------------------------------------------------------------*/

    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->keys = array();
    }

    /*------------------------------------------------------------------------*\
    | Public methods                                                           |
    \*------------------------------------------------------------------------*/

    /**
     * Get a value in the bag
     *
     * @param string  $key
     * @param boolean $suppressException
     *
     * @return mixed
     *
     * @throws ResourceNotFoundException
     */
    public function get($key, $suppressException = false)
    {
        // Return the key if it exists.
        if (array_key_exists($key, $this->keys)) {
            return $this->container[$this->generateKey($key)];
        }

        // Suppress the exception, return null.
        if ($suppressException === true) {
            return null;
        }

        // Throw an exception.
        throw ResourceNotFoundException::bagParameterNotFoundException(get_class($this), $key);
    }

    /**
     * Set a value in the bag
     *
     * @param $key
     * @param $value
     *
     * @return AbstractBag
     */
    public function set($key, $value)
    {
        $this->container[$this->generateKey($key)] = $value;
        $this->keys[$key] = null;

        return $this;
    }

    /**
     * Checks if a value exists
     *
     * @param string $key
     *
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->keys);
    }

    /**
     * Retrieves all keys in the bag
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->keys);
    }

    /*------------------------------------------------------------------------*\
    | Protected methods                                                        |
    \*------------------------------------------------------------------------*/

    /**
     * Generates a key for the container
     *
     * @param string $key
     *
     * @return string
     */
    protected function generateKey($key)
    {
        return $key;
    }
}
