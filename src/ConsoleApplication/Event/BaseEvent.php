<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\Event;

use ConsoleApplication\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\Event;

abstract class BaseEvent extends Event
{
    /*------------------------------------------------------------------------*\
    | Attributes                                                               |
    \*------------------------------------------------------------------------*/

    /**
     * @var Container
     */
    protected $container;

    /*------------------------------------------------------------------------*\
    | Constructor                                                              |
    \*------------------------------------------------------------------------*/

    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /*------------------------------------------------------------------------*\
    | Getters and setters                                                      |
    \*------------------------------------------------------------------------*/

    /**
     * Retrieves the application container
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
