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

use ConsoleApplication\DependencyInjection\Container;
use ConsoleApplication\Event\Events;
use ConsoleApplication\Event\Run\PostRunEvent;
use ConsoleApplication\Event\Run\PreRunEvent;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @param string    $name
     * @param string    $version
     */
    public function __construct(Container $container, $name, $version)
    {
        // Call parent constructor.
        parent::__construct($name, $version);

        // Set container and add commands.
        $this->container = $container;

        // Set the app name and version.
        $applicationBag = $this->container->getApplicationBag();
        $applicationBag
            ->setName($name)
            ->setVersion($version);

        // Set event dispatcher.
        $serviceBag = $this->container->getServiceBag();
        $this->setDispatcher($serviceBag->getDispatcher());
    }

    /**
     * @inheritdoc
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        // Define the running environment and initialize the container.
        $env = $input->getParameterOption(array('-e', '--env'), 'dev');
        $this->container->getApplicationBag()->setEnv($env);
        $this->container->getConsoleBag()
            ->set('input', $input)
            ->set('output', $output);
        $this->container->initialize();

        // Add commands.
        foreach ($this->container->getCommandBag()->values() as $command) {
            $this->add($command);
        }

        // Dispatch pre run event.
        $dispatcher = $this->container->getServiceBag()->getDispatcher();
        $dispatcher->dispatch(Events::PRE_RUN, new PreRunEvent($this->container));

        // Run application.
        $result = parent::doRun($input, $output);

        // Dispatch post run event.
        $dispatcher->dispatch(Events::POST_RUN, new PostRunEvent($this->container, $result));

        return $result;
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
