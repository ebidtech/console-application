<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\DependencyInjection;

use ConsoleApplication\Exception\ConfigurationException;

interface EventSubscriberInterface extends \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /*------------------------------------------------------------------------*\
    | Public methods                                                           |
    \*------------------------------------------------------------------------*/

    /**
     * Register a event subscriber in the given container
     *
     * @param Container $container
     *
     * @throws ConfigurationException
     */
    public function initialize(Container $container);
}
