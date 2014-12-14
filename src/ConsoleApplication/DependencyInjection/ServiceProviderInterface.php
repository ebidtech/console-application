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

interface ServiceProviderInterface
{
    /*------------------------------------------------------------------------*\
    | Public methods                                                           |
    \*------------------------------------------------------------------------*/

    /**
     * Register a service provider in the given container
     *
     * @param Container
     *
     * @throws ConfigurationException
     */
    public function register(Container $container);
}
