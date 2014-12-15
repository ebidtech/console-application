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
        return new static(sprintf('Method "%s" for class "%s" is not implemented/accessible.', $method, $class));
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
        return new static(sprintf('Class "%s" must override the method "%s" of the base class.', $class, $method));
    }

    /**
     * Must implement interface exception
     *
     * @param string $interface
     * @param string $class
     *
     * @return LogicException
     */
    public static function mustImplementInterfaceException($interface, $class)
    {
        return new static(sprintf('Class "%s" must implement interface "%s".', $class, $interface));
    }

    /**
     * Must extend class exception
     *
     * @param string $toExtend
     * @param string $class
     *
     * @return LogicException
     */
    public static function mustExtendClassException($toExtend, $class)
    {
        return new static(sprintf('Class "%s" must extend class "%s".', $class, $toExtend));
    }

    /**
     * Class not found exception
     *
     * @param string $class
     *
     * @return LogicException
     */
    public static function classNotFoundException($class)
    {
        return new static(sprintf('Class "%s" could not be found.', $class));
    }

    /**
     * Class not instantiable exception
     *
     * @param string $class
     *
     * @return LogicException
     */
    public static function classNotInstantiableException($class)
    {
        return new static(sprintf('Class "%s" is not instantiable.', $class));
    }

    public static function invalidNumberOfParametersException($class, $method, $number)
    {
        return new static(
            sprintf(
                'Method "%s" of class "%s" should have %d (required) parameters.',
                $method,
                $class,
                $number
            )
        );
    }
}
