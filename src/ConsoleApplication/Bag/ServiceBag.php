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

use ConsoleApplication\DependencyInjection\Loader\FileLoaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServiceBag extends AbstractBag
{
    /*------------------------------------------------------------------------*\
    | Constants                                                                |
    \*------------------------------------------------------------------------*/

    /**
     * @const string
     */
    const SERVICE_BAG_BASE_KEY = 'service';

    /**
     * @const string
     */
    const DEFAULT_SERVICE_LOGGER = 'logger';

    /**
     * @const string
     */
    const DEFAULT_SERVICE_DISPATCHER = 'dispatcher';

    /**
     * @const string
     */
    const DEFAULT_SERVICE_FILE_LOADER = 'file_loader';

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
        return sprintf('%s.%s', self::SERVICE_BAG_BASE_KEY, $key);
    }

    /*------------------------------------------------------------------------*\
    | Getters and setters                                                      |
    \*------------------------------------------------------------------------*/

    /**
     * Retrieves the logger service
     *
     * @param boolean $suppressException
     *
     * @return LoggerInterface
     */
    public function getLogger($suppressException = false)
    {
        return $this->get(self::DEFAULT_SERVICE_LOGGER, $suppressException);
    }

    /**
     * Sets the logger service
     *
     * @param LoggerInterface $logger
     *
     * @return ServiceBag
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->set(self::DEFAULT_SERVICE_LOGGER, $logger);

        return $this;
    }

    /**
     * Retrieves the dispatcher service
     *
     * @param boolean $suppressException
     *
     * @return EventDispatcherInterface
     */
    public function getDispatcher($suppressException = false)
    {
        return $this->get(self::DEFAULT_SERVICE_DISPATCHER, $suppressException);
    }

    /**
     * Sets the event dispatcher service
     *
     * @param EventDispatcherInterface $dispatcher
     *
     * @return ServiceBag
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->set(self::DEFAULT_SERVICE_DISPATCHER, $dispatcher);

        return $this;
    }

    /**
     * Retrieves the file loader service
     *
     * @param boolean $suppressException
     *
     * @return FileLoaderInterface
     */
    public function getFileLoader($suppressException = false)
    {
        return $this->get(self::DEFAULT_SERVICE_FILE_LOADER, $suppressException);
    }

    /**
     * Sets the file loader service
     *
     * @param FileLoaderInterface $fileLoader
     *
     * @return ServiceBag
     */
    public function setFileLoader(FileLoaderInterface $fileLoader)
    {
        $this->set(self::DEFAULT_SERVICE_FILE_LOADER, $fileLoader);

        return $this;
    }
}
