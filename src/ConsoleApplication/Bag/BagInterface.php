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

interface BagInterface extends \ArrayAccess
{
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
    public function get($key, $suppressException = false);

    /**
     * Set a value in the bag
     *
     * @param $key
     * @param $value
     *
     * @return AbstractBag
     */
    public function set($key, $value);

    /**
     * Remove a value from the bag
     *
     * @param string  $key
     *
     * @return AbstractBag
     */
    public function delete($key);

    /**
     * Checks if a value exists
     *
     * @param string $key
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Retrieves all keys in the bag
     *
     * @return array
     */
    public function keys();

    /**
     * Retrieves all values in the bag
     *
     * @return array
     */
    public function values();
}
