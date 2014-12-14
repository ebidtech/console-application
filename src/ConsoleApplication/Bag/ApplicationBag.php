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

class ApplicationBag extends AbstractBag
{
    /*------------------------------------------------------------------------*\
    | Constants                                                                |
    \*------------------------------------------------------------------------*/

    /**
     * @const string
     */
    const APPLICATION_BAG_BASE_KEY = 'application';

    /**
     * @const string
     */
    const APPLICATION_DIR_BASE_KEY = 'dir';

    /**
     * @const string
     */
    const APPLICATION_NAME = 'name';

    /**
     * @const string
     */
    const APPLICATION_VERSION = 'version';

    /**
     * @const string
     */
    const APPLICATION_CHARSET = 'charset';

    /**
     * @const string
     */
    const APPLICATION_ENV = 'env';

    /*------------------------------------------------------------------------*\
    | Protected methods                                                        |
    \*------------------------------------------------------------------------*/

    /**
     * Generates a key for the container
     *
     * @param string $key
     *
     * @return string
     */
    protected function generateKey($key)
    {
        return sprintf('%s.%s', self::APPLICATION_BAG_BASE_KEY, $key);
    }

    /*------------------------------------------------------------------------*\
    | Getters and setters                                                      |
    \*------------------------------------------------------------------------*/

    /**
     * Retrieves the application name
     *
     * @param boolean $suppressException
     *
     * @return string
     */
    public function getName($suppressException = false)
    {
        return $this->get(self::APPLICATION_NAME, $suppressException);
    }

    /**
     * Set the application name
     *
     * @param string $name
     *
     * @return ApplicationBag
     */
    public function setName($name)
    {
        $this->set(self::APPLICATION_NAME, $name);

        return $this;
    }

    /**
     * Retrieves the application version
     *
     * @param boolean $suppressException
     *
     * @return string
     */
    public function getVersion($suppressException = false)
    {
        return $this->get(self::APPLICATION_VERSION, $suppressException);
    }

    /**
     * Set the application version
     *
     * @param string $version
     *
     * @return ApplicationBag
     */
    public function setVersion($version)
    {
        $this->set(self::APPLICATION_VERSION, $version);

        return $this;
    }

    /**
     * Retrieves the application charset
     *
     * @param boolean $suppressException
     *
     * @return string
     */
    public function getCharset($suppressException = false)
    {
        return $this->get(self::APPLICATION_CHARSET, $suppressException);
    }

    /**
     * Set the application charset
     *
     * @param string $charset
     *
     * @return ApplicationBag
     */
    public function setCharset($charset)
    {
        $this->set(self::APPLICATION_CHARSET, $charset);

        return $this;
    }

    /**
     * Retrieves the application environment
     *
     * @param boolean $suppressException
     *
     * @return string
     */
    public function getEnv($suppressException = false)
    {
        return $this->get(self::APPLICATION_ENV, $suppressException);
    }

    /**
     * Set the application environment
     *
     * @param string $env
     *
     * @return ApplicationBag
     */
    public function setEnv($env)
    {
        $this->set(self::APPLICATION_ENV, $env);

        return $this;
    }

    /**
     * Retrieves a directory
     *
     * @param string  $key
     * @param boolean $suppressException
     *
     * @return string
     */
    public function getDirectory($key, $suppressException = false)
    {
        return $this->get(sprintf('%s.%s', self::APPLICATION_DIR_BASE_KEY, $key), $suppressException);
    }

    /**
     * Set a directory
     *
     * @param string $key
     * @param string $directory
     *
     * @return ApplicationBag
     */
    public function setDirectory($key, $directory)
    {
        $this->set(sprintf('%s.%s', self::APPLICATION_DIR_BASE_KEY, $key), $directory);

        return $this;
    }
}
