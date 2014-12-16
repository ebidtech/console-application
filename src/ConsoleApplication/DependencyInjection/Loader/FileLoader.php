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

use ConsoleApplication\Bag\BagInterface;
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
        if ($root !== null && !array_key_exists($root, $config)) {
            // Just return the bag if exception is suppressed.
            if ($suppressException === true) {
                return $bag;
            }

            // Throw a file root not found error.
            throw ConfigurationException::configurationFileRootNotFoundException($filename, $root);
        }

        // Point to the root.
        if ($root !== null) {
            $config = $config[$root];
        }

        // Load values to the bag and return it.
        return $this->loadToBag($bag, is_array($config) ? $config : array(), $recursive);
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
            // Get value and placeholder.
            $value = $bag->get($key);
            $placeholder = $this->getPlaceholder($value);

            // Resolve directly.
            if (is_string($placeholder)) {
                if (!($parameters->has($placeholder))) {
                    throw ConfigurationException::configurationParameterNotFoundException($placeholder);
                }

                // Set parameter.
                $bag->set($key, $parameters->get($placeholder));
                continue;
            }

            // Resolve recursively as array.
            if (is_array($value)) {
                $bag->set($key, $this->resolveParametersRecursively($value, $parameters));
                continue;
            }
        }

        return $bag;
    }

    /*------------------------------------------------------------------------*\
    | Private methods                                                          |
    \*------------------------------------------------------------------------*/

    /**
     * Resolves parameters in an array using a bag of parameters recursively
     *
     * @param array        $values
     * @param BagInterface $parameters
     *
     * @return array
     *
     * @throws ConfigurationException
     */
    private function resolveParametersRecursively(array $values, BagInterface $parameters)
    {
        foreach ($values as $key => &$value) {
            $placeholder = $this->getPlaceholder($value);

            // Resolve directly.
            if (is_string($placeholder)) {
                if (!($parameters->has($placeholder))) {
                    throw ConfigurationException::configurationParameterNotFoundException($placeholder);
                }

                // Set parameter.
                $values[$key] = $parameters->get($placeholder);
                continue;
            }

            // Resolve recursively as array.
            if (is_array($value)) {
                $values[$key] = $this->resolveParametersRecursively($value, $parameters);
                continue;
            }
        }

        return $values;
    }

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
            $key = $prefix === null ? $key : sprintf('%s.%s', $prefix, $key);

            // Recursively set values in bag.
            if (is_array($value) && $this->isAssociativeArray($value) && $recursive === true) {
                // Calculate prefix and call method recursively.
                $this->loadToBag($bag, $value, $recursive, $key);
                continue;
            }

            // Load values in place (no recursion).
            $bag->set($key, $value);
        }

        // Return the bag.
        return $bag;
    }


    /**
     * Checks if an array is associative (a non associative array
     * has only numeric, ordered and sequential keys starting at 0)
     *
     * @param array $array
     * @return boolean
     */
    private function isAssociativeArray(array $array)
    {
        return ($array !== array_values($array));
    }

    /**
     * Retrieve the placeholder name or false in case there is no placeholder
     *
     * @param string $value
     *
     * @return string|false
     */
    private function getPlaceholder($value)
    {
        // Placeholders can only be strings.
        if (!is_string(($value))) {
            return false;
        }
        $matches = array();

        // Check if is placeholder.
        if (preg_match(self::PLACEHOLDER_REGEX, $value, $matches) > 0) {
            return $matches[0];
        }

        return false;
    }
}
