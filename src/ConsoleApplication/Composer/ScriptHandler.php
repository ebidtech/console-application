<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\Composer;

use Composer\Config;
use Composer\Package\Package;
use Composer\Script\CommandEvent;
use Symfony\Component\Filesystem\Filesystem;

class ScriptHandler
{
    /*------------------------------------------------------------------------*\
    | Constants                                                                |
    \*------------------------------------------------------------------------*/

    /**
     * @const string
     */
    const CONSOLE_APPLICATION_BASE_DIR = 'console-application-base-dir';

    /**
     * @const string
     */
    const CONSOLE_APPLICATION_APP_DIR = 'console-application-app-dir';

    /**
     * @const string
     */
    const CONSOLE_APPLICATION_CONFIG_DIR = 'console-application-config-dir';

    /*------------------------------------------------------------------------*\
    | Static attributes                                                        |
    \*------------------------------------------------------------------------*/

    /**
     * @var array
     */
    private static $options = array(
        self::CONSOLE_APPLICATION_APP_DIR => 'app'
    );

    /*------------------------------------------------------------------------*\
    | Public static methods                                                    |
    \*------------------------------------------------------------------------*/

    /**
     * Retrieves the path options
     *
     * @return array
     */
    public static function getOptions()
    {
        return self::$options;
    }

    /**
     * Copies the bootstrap directory structure
     *
     * @param CommandEvent $event
     */
    public static function buildBootstrap(CommandEvent $event)
    {
        // Build path.
        $fs = new Filesystem();
        static::buildPathFromBase($event, self::CONSOLE_APPLICATION_APP_DIR);
        $directory = self::$options[self::CONSOLE_APPLICATION_APP_DIR];

        // Throw exception if directory exists.
        if ($fs->exists($directory)) {
            //@TODO It can be a good idea to throw an exception, but just ignore if for now.
            return;
        }

        // Copy folder.
        $fs->copy(realpath(sprintf('%s%s', __DIR__, '/../Resources/skeleton/app')), $directory);
    }

    /*------------------------------------------------------------------------*\
    | Private static methods                                                   |
    \*------------------------------------------------------------------------*/

    /**
     * Build a directory path starting on the project base
     *
     * @param CommandEvent $event
     * @param string       $directory
     */
    private static function buildPathFromBase(CommandEvent $event, $directory)
    {
        /** @var Package $package */
        $package = $event->getComposer()->getPackage();
        $extra = $package->getExtra();

        // Generate the complete path.
        $basePath = getcwd();
        $path = array_key_exists($directory, $extra) ? $extra[$directory] : self::$options[$directory];
        $completePath = sprintf('%s/%s', $basePath, $path);

        // Set values.
        self::$options[self::CONSOLE_APPLICATION_BASE_DIR] = $basePath;
        self::$options[$directory] = $completePath;
    }
}
