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

class CommandBag extends AbstractBag
{
    /*------------------------------------------------------------------------*\
    | Constants                                                                |
    \*------------------------------------------------------------------------*/

    /**
     * @const string
     */
    const COMMAND_BAG_BASE_KEY = 'command';

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
        return sprintf('%s.%s', self::COMMAND_BAG_BASE_KEY, $key);
    }
}
