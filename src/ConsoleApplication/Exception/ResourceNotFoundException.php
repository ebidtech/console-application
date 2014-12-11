<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\Exception;

class ResourceNotFoundException extends RuntimeException
{
    /**
     * Bag parameter not found exception
     *
     * @param string $bag
     * @param string $parameter
     *
     * @return ResourceNotFoundException
     */
    public static function bagParameterNotFoundException($bag, $parameter)
    {
        return new static(sprintf('Parameter "%s" was not found in bag "%s"', $parameter, $bag));
    }
}
