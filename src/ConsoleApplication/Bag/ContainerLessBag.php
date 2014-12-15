<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\Bag;

use ConsoleApplication\Exception\ResourceNotFoundException;

class ContainerLessBag implements BagInterface
{
    /*------------------------------------------------------------------------*\
    | Attributes                                                               |
    \*------------------------------------------------------------------------*/

    protected $values;

    /*------------------------------------------------------------------------*\
    | Constructor                                                              |
    \*------------------------------------------------------------------------*/

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values = array();
    }

    /*------------------------------------------------------------------------*\
    | Public methods                                                           |
    \*------------------------------------------------------------------------*/

    /**
     * Get a value in the bag
     *
     * @param string $key
     * @param boolean $suppressException
     *
     * @return mixed
     *
     * @throws ResourceNotFoundException
     */
    public function get($key, $suppressException = false)
    {
        // Return the key if it exists.
        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
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
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * Remove a value from the bag
     *
     * @param string $key
     *
     * @return AbstractBag
     */
    public function delete($key)
    {
        // Return the key if it exists.
        if (array_key_exists($key, $this->values)) {
            unset($this->values[$key]);
        }

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
        return array_key_exists($key, $this->values);
    }

    /**
     * Retrieves all keys in the bag
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->values);
    }

    /**
     * Retrieves all values in the bag
     *
     * @return array
     */
    public function values()
    {
        return $this->values;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}
