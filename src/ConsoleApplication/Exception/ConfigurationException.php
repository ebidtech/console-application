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

class ConfigurationException extends RuntimeException
{
    /*------------------------------------------------------------------------*\
    | Public static methods                                                    |
    \*------------------------------------------------------------------------*/

    /**
     * Configuration file root not found exception
     *
     * @param string $filename
     * @param string $root
     *
     * @return ConfigurationException
     */
    public static function configurationFileRootNotFoundException($filename, $root)
    {
        return new static(sprintf('Root node "%s" could not be found in file "%"', $root, $filename));
    }

    /**
     * Configuration parameter not found exception
     *
     * @param string $parameter
     *
     * @return ConfigurationException
     */
    public static function configurationParameterNotFoundException($parameter)
    {
        return new static(sprintf('Required configuration parameter "%s" was not defined', $parameter));
    }

    /**
     * Directory already exists exception
     *
     * @param string $directory
     *
     * @return ConfigurationException
     */
    public static function directoryAlreadyExistsException($directory)
    {
        return new static(sprintf('Directory "%s" already exists', $directory));
    }
}
