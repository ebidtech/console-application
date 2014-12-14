<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\Console\Command;

use ConsoleApplication\Console\ConsoleApplication;
use ConsoleApplication\DependencyInjection\Container;
use ConsoleApplication\Event\Events;
use ConsoleApplication\Event\Execute\PostExecuteEvent;
use ConsoleApplication\Event\Execute\PreExecuteEvent;
use ConsoleApplication\Event\Initialize\PostInitializeEvent;
use ConsoleApplication\Event\Initialize\PreInitializeEvent;
use ConsoleApplication\Exception\LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    /*------------------------------------------------------------------------*\
    | Attributes                                                               |
    \*------------------------------------------------------------------------*/

    /**
     * @var ConsoleApplication
     */
    protected $application;

    /**
     * @var Container
     */
    protected $container;

    /*------------------------------------------------------------------------*\
    | Protected methods                                                        |
    \*------------------------------------------------------------------------*/

    /**
     * Execute the command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Dispatch pre execute event.
        $dispatcher = $this->container->getServiceBag()->getDispatcher();
        $dispatcher->dispatch(Events::PRE_EXECUTE, new PreExecuteEvent($this->container));

        // Execute command.
        $this->executeCommand($input, $output);

        // Dispatch post execute event.
        $dispatcher->dispatch(Events::POST_EXECUTE, new PostExecuteEvent($this->container));
    }

    /**
     * Executes a command (must be overridden)
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function executeCommand(InputInterface $input, OutputInterface $output)
    {
        throw LogicException::mustOverrideMethodException(__METHOD__, get_class($this));
    }

    /**
     * Initializes default command definitions
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    final protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // Set container and application.
        $this->application = parent::getApplication();
        $this->container = $this->application->getContainer();

        // Dispatch pre execute event.
        $dispatcher = $this->container->getServiceBag()->getDispatcher();
        $dispatcher->dispatch(Events::PRE_INITIALIZE, new PreInitializeEvent($this->container));

        // Initialize child class.
        $this->initializeCommand($input, $output);

        // Dispatch post execute event.
        $dispatcher->dispatch(Events::POST_INITIALIZE, new PostInitializeEvent($this->container));
    }

    /**
     * Initializes a command (not mandatory, but can be overridden)
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initializeCommand(InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * Configure default command parameters
     */
    protected function configure()
    {
        // Maintain parent parameters (like verbosity).
        parent::configure();

        // Default options for every command.
        $this
            ->addOption('env', 'e', InputOption::VALUE_REQUIRED, 'Set the application environment.', 'dev');
    }

    /*------------------------------------------------------------------------*\
    | Getters and setters                                                      |
    \*------------------------------------------------------------------------*/

    /**
     * Retrieves the application instance
     *
     * @return ConsoleApplication
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Retrieves the container instance
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
