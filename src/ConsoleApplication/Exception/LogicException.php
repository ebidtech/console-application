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

class LogicException extends RuntimeException
{
    /*------------------------------------------------------------------------*\
    | Public static methods                                                    |
    \*------------------------------------------------------------------------*/

    /**
     * Method not implemented exception
     *
     * @param string $method
     * @param string $class
     *
     * @return LogicException
     */
    public static function methodNotImplementedException($method, $class)
    {
        return new static(sprintf('Method "%s" for class "%s" is not implemented/accessible', $method, $class));
    }

    /**
     * Must override method exception
     *
     * @param string $method
     * @param string $class
     *
     * @return LogicException
     */
    public static function mustOverrideMethodException($method, $class)
    {
        return new static(sprintf('Class "%s" must override the method "%s" of the base class', $class, $method));
    }
}
