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

class EventSubscriberBag extends AbstractBag
{
    /*------------------------------------------------------------------------*\
    | Constants                                                                |
    \*------------------------------------------------------------------------*/

    /**
     * @const string
     */
    const EVENT_SUBSCRIBER_BAG_BASE_KEY = 'event_subscriber';

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
        return sprintf('%s.%s', self::EVENT_SUBSCRIBER_BAG_BASE_KEY, $key);
    }
}
