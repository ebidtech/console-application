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

final class Events
{
    /*------------------------------------------------------------------------*\
    | Constants                                                                |
    \*------------------------------------------------------------------------*/

    /**
     * @const string
     */
    const PRE_EXECUTE = 'pre_execute';

    /**
     * @const string
     */
    const POST_EXECUTE = 'post_execute';

    /**
     * @const string
     */
    const PRE_INITIALIZE = 'pre_initialize';

    /**
     * @const string
     */
    const POST_INITIALIZE = 'post_initialize';
}
