<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\Console;

use ConsoleApplication\Console\Command\DummyCommand;
use ConsoleApplication\DependencyInjection\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

class ConsoleApplication extends Application
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
        // Call parent constructor.
        parent::__construct();

        // Set container and add commands.
        $this->container = $container;

        // Set the app name and version.
        $applicationBag = $this->container->getApplicationBag();
        $this->setName($applicationBag->get('name'));
        $this->setVersion($applicationBag->get('version'));

        //@TODO REMOVE
        $this->add(new DummyCommand());
    }

    /*------------------------------------------------------------------------*\
    | Getters and setters                                                      |
    \*------------------------------------------------------------------------*/

    /**
     * Retrieves the application container.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
