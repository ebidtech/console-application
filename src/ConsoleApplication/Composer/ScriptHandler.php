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
        static::buildPathFromBase($event, self::CONSOLE_APPLICATION_APP_DIR);
        $directory = self::$options[self::CONSOLE_APPLICATION_APP_DIR];

        if (file_exists($directory)) {
            echo 'Directory "app" already exists, will not build bootstrap.';
            return;
        }

        // Copy folder.
        xcopy(realpath(sprintf('%s%s', __DIR__, '/../Resources/skeleton/app')), $directory);
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

    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @param       string   $source
     * @param       string   $destination
     * @param       integer  $permissions
     *
     * @return      boolean
     */
    private static function xcopy($source, $destination, $permissions = 0755)
    {
        // Check for symlinks.
        if (is_link($source)) {
            return symlink(readlink($source), $destination);
        }

        // Simple copy for a file.
        if (is_file($source)) {
            return copy($source, $destination);
        }

        // Make destination directory.
        if (!is_dir($destination)) {
            mkdir($destination, $permissions);
        }

        // Loop through the folder.
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers.
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories.
            static::xcopy("$source/$entry", "$destination/$entry", $permissions);
        }

        // Clean up.
        $dir->close();
        return true;
    }
}
