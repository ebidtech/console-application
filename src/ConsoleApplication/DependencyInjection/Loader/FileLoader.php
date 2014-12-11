<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\DependencyInjection\Loader;

use ConsoleApplication\DependencyInjection\Bag\BagInterface;
use ConsoleApplication\Exception\ConfigurationException;
use ConsoleApplication\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;

class FileLoader implements FileLoaderInterface
{
    /*------------------------------------------------------------------------*\
    | Constants                                                                |
    \*------------------------------------------------------------------------*/

    /**
     * @const string
     */
    const PLACEHOLDER_REGEX = '/(?<=^%)\w+(?=%$)/';

    /*------------------------------------------------------------------------*\
    | Public methods                                                           |
    \*------------------------------------------------------------------------*/

    /**
     * Loads a file configuration to a bag
     *
     * @param string       $filename
     * @param string       $root
     * @param BagInterface $bag
     * @param boolean      $recursive
     * @param boolean      $suppressException
     *
     * @return BagInterface
     *
     * @throws ConfigurationException
     * @throws FileNotFoundException
     */
    public function loadFileToBag($filename, $root, BagInterface $bag, $recursive = false, $suppressException = false)
    {
        // Check for the files' existence.
        if (!file_exists($filename)) {
            // Just return the bag if exception is suppressed.
            if ($suppressException === true) {
                return $bag;
            }

            // Throw a file not found error.
            throw FileNotFoundException::configurationFileNotFoundException($filename);
        }

        // Parse YAML file.
        $yaml = new Yaml();
        $config = $yaml->parse(file_get_contents($filename));

        // Check if the root exists.
        if (!array_key_exists($root, $config)) {
            // Just return the bag if exception is suppressed.
            if ($suppressException === true) {
                return $bag;
            }

            // Throw a file root not found error.
            throw ConfigurationException::configurationFileRootNotFoundException($filename, $root);
        }

        // Load values to the bag and return it.
        return $this->loadToBag($bag, is_array($config[$root]) ?: array(), $recursive);
    }

    /**
     * Resolves parameters in a bag using another bag of parameters
     *
     * @param BagInterface $bag
     * @param BagInterface $parameters
     *
     * @return BagInterface
     *
     * @throws ConfigurationException
     */
    public function resolveParameters(BagInterface $bag, BagInterface $parameters)
    {
        // Iterate every bag key.
        foreach ($bag->keys() as $key) {
            $value = $bag->get($key);
            $matches = array();

            // Checks if the config is a placeholder.
            if (preg_match(self::PLACEHOLDER_REGEX, $value, $matches) > 0) {
                // Checks if the parameter exists, otherwise throw an exception.
                if (!($parameters->has($matches[0]))) {
                    throw ConfigurationException::configurationParameterNotFoundException($matches[0]);
                }

                // Set the parameter value.
                $bag->set($key, $matches[0]);
            }
        }

        return $bag;
    }

    /*------------------------------------------------------------------------*\
    | Private methods                                                          |
    \*------------------------------------------------------------------------*/

    /**
     * Loads values to a bag
     *
     * @param BagInterface $bag
     * @param array        $values
     * @param boolean      $recursive
     * @param string|null  $prefix
     *
     * @return BagInterface
     */
    private function loadToBag(BagInterface $bag, array $values, $recursive = false, $prefix = null)
    {
        // Iterate every value.
        foreach ($values as $key => &$value) {
            // Recursively set values in bag.
            if (is_array($value) && $recursive === true) {
                // Calculate prefix and call method recursively.
                $prefix = $prefix === null ? $key : sprintf('%s.%s', $prefix, $key);
                $this->loadToBag($bag, $value, $recursive, $prefix);
                continue;
            }

            // Load values in place (no recursion).
            $bag->set($key, $value);
        }

        // Return the bag.
        return $bag;
    }
}
