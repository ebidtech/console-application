<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\EventSubscriber;

use ConsoleApplication\DependencyInjection\Container;
use ConsoleApplication\DependencyInjection\EventSubscriberInterface;
use ConsoleApplication\Event\Events;
use ConsoleApplication\Event\Initialize\PostInitializeEvent;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class InitializeEventSubscriber implements EventSubscriberInterface
{
    /*------------------------------------------------------------------------*\
    | Public methods                                                           |
    \*------------------------------------------------------------------------*/

    /**
     * Creates a logger after command initialization
     *
     * @param PostInitializeEvent $event
     */
    public function onPostInitialize(PostInitializeEvent $event)
    {
        // Get bags.
        $container = $event->getContainer();
        $services = $container->getServiceBag();
        $console = $container->getConsoleBag();

        // Create a new logger if a valid output stream was defined.
        if ($console->has('output') && ($console->get('output') instanceof OutputInterface)) {
            $services->setLogger(new ConsoleLogger($console->get('output')));
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::POST_INITIALIZE => 'onPostInitialize'
        );
    }

    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container->getServiceBag()->getDispatcher()->addSubscriber($this);
    }
}
