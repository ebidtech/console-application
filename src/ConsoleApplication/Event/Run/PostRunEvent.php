<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\Event\Run;

use ConsoleApplication\DependencyInjection\Container;
use ConsoleApplication\Event\BaseEvent;

class PostRunEvent extends BaseEvent
{
    /*------------------------------------------------------------------------*\
    | Attributes                                                               |
    \*------------------------------------------------------------------------*/

    /**
     * @var integer
     */
    protected $result;

    /*------------------------------------------------------------------------*\
    | Constructor                                                              |
    \*------------------------------------------------------------------------*/

    /**
     * Constructor
     *
     * @param Container $container
     * @param integer   $result
     */
    public function __construct(Container $container, $result)
    {
        parent::__construct($container);
        $this->result = $result;
    }

    /*------------------------------------------------------------------------*\
    | Getters and setters                                                      |
    \*------------------------------------------------------------------------*/

    /**
     * Retrieve the result
     *
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }
}
