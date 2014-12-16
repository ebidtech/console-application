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
        return new static(sprintf('Root node "%s" could not be found in file "%s".', $root, $filename));
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
        return new static(sprintf('Required configuration parameter "%s" was not defined.', $parameter));
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
        return new static(sprintf('Directory "%s" already exists.', $directory));
    }

    /**
     * Configuration attribute not found exception
     *
     * @param string $filename
     * @param string $parent
     * @param string $attribute
     *
     * @return ConfigurationException
     */
    public static function configurationAttributeNotFoundException($filename, $parent, $attribute)
    {
        return new static(
            sprintf(
                'Required configuration attribute "%s" for "%s" could not be found in file "%s".',
                $attribute,
                $parent,
                $filename
            )
        );
    }

    /**
     * Multiple service definitions exception
     *
     * @param string $service
     *
     * @return ConfigurationException
     */
    public static function multipleServiceDefinitionsException($service)
    {
        return new static(sprintf('Multiple definitions for service "%s".', $service));
    }

    /**
     * Multiple event subscriber definitions exception
     *
     * @param string $eventSubscriber
     *
     * @return ConfigurationException
     */
    public static function multipleEventSubscriberDefinitionsException($eventSubscriber)
    {
        return new static(sprintf('Multiple definitions for event subscriber "%s".', $eventSubscriber));
    }

    /**
     * Multiple command definitions exception
     *
     * @param string $command
     *
     * @return ConfigurationException
     */
    public static function multipleCommandDefinitionsException($command)
    {
        return new static(sprintf('Multiple definitions for command "%s".', $command));
    }
}
