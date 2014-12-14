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

use ConsoleApplication\Bag\ApplicationBag;
use ConsoleApplication\Bag\CommandBag;
use ConsoleApplication\Bag\ConfigurationBag;
use ConsoleApplication\Bag\ConsoleBag;
use ConsoleApplication\Bag\EventSubscriberBag;
use ConsoleApplication\Bag\ParameterBag;
use ConsoleApplication\Bag\ServiceBag;
use ConsoleApplication\DependencyInjection\Loader\FileLoader;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Container extends \Pimple\Container
{
    /*------------------------------------------------------------------------*\
    | Attributes                                                               |
    \*------------------------------------------------------------------------*/

    /**
     * @var boolean
     */
    private $initialized;

    /*------------------------------------------------------------------------*\
    | Constructor                                                              |
    \*------------------------------------------------------------------------*/

    /**
     * Creates a new container
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        // Mark container as uninitialized.
        $this->initialized = false;

        // Create bags and set default values.
        $this->createBags();
        $this->setDefaults($directory);

        // Load essential services.
        $this->loadEssentialServices();
    }

    /*------------------------------------------------------------------------*\
    | Public methods                                                           |
    \*------------------------------------------------------------------------*/

    /**
     * Initializes configurations when needed, not in constructor
     * so that the application can have some control
     */
    public function initialize()
    {
        // Initialize only the first time.
        if ($this->initialized === false) {
            // Rebuild the logger if a new output is set.
            if ($this->getConsoleBag()->has('output')) {
                $this->getServiceBag()->setLogger(
                    new ConsoleLogger($this->getConsoleBag()->get('output'))
                );
            }

            // Load files and classes.
            $this->loadFiles();
            $this->loadClasses();
            $this->initialized = true;
        }
    }

    /**
     * Registers a new service
     *
     * @param ServiceProviderInterface $serviceProvider
     */
    public function registerService(ServiceProviderInterface $serviceProvider)
    {
        $serviceProvider->register($this);
    }

    /**
     * Registers a new event subscriber
     *
     * @param EventSubscriberInterface $eventSubscriber
     */
    public function registerEventSubscriber(EventSubscriberInterface $eventSubscriber)
    {
        $eventSubscriber->register($this);
    }

    /*------------------------------------------------------------------------*\
    | Private methods                                                          |
    \*------------------------------------------------------------------------*/

    /**
     * Creates all default bags
     */
    private function createBags()
    {
        $this[ApplicationBag::APPLICATION_BAG_BASE_KEY] = new ApplicationBag($this);
        $this[ConsoleBag::CONSOLE_BAG_BASE_KEY] = new ConsoleBag($this);
        $this[ParameterBag::PARAMETER_BAG_BASE_KEY] = new ParameterBag($this);
        $this[ServiceBag::SERVICE_BAG_BASE_KEY] = new ServiceBag($this);
        $this[ConfigurationBag::CONFIGURATION_BAG_BASE_KEY] = new ConfigurationBag($this);
    }

    /**
     * Set default values
     *
     * @param string $directory
     */
    private function setDefaults($directory)
    {
        // Application configurations.
        $applicationBag = $this->getApplicationBag();
        $applicationBag->setCharset('UTF-8');

        // Useful directories.
        $applicationBag
            ->setDirectory('base', sprintf('%s/', realpath($directory)))
            ->setDirectory('app', sprintf('%s%s', $applicationBag->getDirectory('base'), 'app/'))
            ->setDirectory('config', sprintf('%s%s', $applicationBag->getDirectory('base'), 'app/config/'));
    }

    /**
     * Loads essential, always present services
     */
    private function loadEssentialServices()
    {
        // Get service bag.
        $serviceBag = $this->getServiceBag();

        // Logger (very verbose log by default, it will be replaced later).
        $serviceBag->setLogger(new ConsoleLogger(new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG)));

        // Event dispatcher.
        $serviceBag->setDispatcher(new EventDispatcher());

        // File loader.
        $serviceBag->setFileLoader(new FileLoader());
    }

    /**
     * Load files
     */
    private function loadFiles()
    {
        // Load configuration and parameters.
        $this->loadParametersFile();
        $this->loadConfigurationsFile();

        // Load services.
        $this->loadServicesFile();

        // Load event subscribers.
        $this->loadEventSubscribersFile();

        // Load commands.
        $this->loadCommandsFile();
    }

    /**
     * Load parameters
     */
    private function loadParametersFile()
    {
        // Get useful values.
        $fileLoader = $this->getServiceBag()->getFileLoader();
        $applicationBag = $this->getApplicationBag();

        // Load general parameters.
        $fileLoader->loadFileToBag(
            $this->generateConfigFilePath('parameters.yml'),
            'parameters',
            $this->getParameterBag()
        );

        // Load environment parameters (ignore if file does not exist).
        if ($applicationBag->has('env')) {
            $env = $applicationBag->get('env');
            $fileLoader->loadFileToBag(
                $this->generateConfigFilePath(sprintf('%s_%s.yml', 'parameters', $env)),
                'parameters',
                $this->getParameterBag(),
                false,
                true
            );
        }
    }

    /**
     * Load configuration
     */
    private function loadConfigurationsFile()
    {
        // Load config.
        $fileLoader = $this->getServiceBag()->getFileLoader();
        $fileLoader->loadFileToBag(
            $this->generateConfigFilePath('config.yml'),
            'config',
            $this->getConfigurationBag(),
            true
        );

        // Resolve parameters.
        $fileLoader->resolveParameters($this->getConfigurationBag(), $this->getParameterBag());
    }

    /**
     * Loads services from configuration
     */
    private function loadServicesFile()
    {
        //@TODO
    }

    /**
     * Loads event subscribers from configuration
     */
    private function loadEventSubscribersFile()
    {
        //@TODO
    }

    /**
     * Loads commands from configuration
     */
    private function loadCommandsFile()
    {
        //@TODO
    }

    /**
     * Load classes
     */
    private function loadClasses()
    {
        // Load service classes.
        $this->loadServices();

        // Load event subscriber classes.
        $this->loadEventSubscribers();

        // Load command classes.
        $this->loadCommands();
    }

    /**
    * Loads service classes
    */
    private function loadServices()
    {
        //@TODO
    }

    /**
     * Loads event subscriber classes
     */
    private function loadEventSubscribers()
    {
        //@TODO
    }

    /**
     * Loads command classes
     */
    private function loadCommands()
    {
        //@TODO
    }

    /**
     * Generate a configuration file path
     *
     * @param string $filename
     *
     * @return string
     */
    private function generateConfigFilePath($filename)
    {
        return sprintf('%s%s', $this->getApplicationBag()->getDirectory('config'), $filename);
    }

    /*------------------------------------------------------------------------*\
    | Getters and setters                                                      |
    \*------------------------------------------------------------------------*/

    /**
     * Retrieves the console bag
     *
     * @return ConsoleBag
     */
    public function getConsoleBag()
    {
        return $this[ConsoleBag::CONSOLE_BAG_BASE_KEY];
    }

    /**
     * Retrieves the application bag
     *
     * @return ApplicationBag
     */
    public function getApplicationBag()
    {
        return $this[ApplicationBag::APPLICATION_BAG_BASE_KEY];
    }

    /**
     * Retrieves the parameter bag
     *
     * @return ParameterBag
     */
    public function getParameterBag()
    {
        return $this[ParameterBag::PARAMETER_BAG_BASE_KEY];
    }

    /**
     * Retrieves the service bag
     *
     * @return ServiceBag
     */
    public function getServiceBag()
    {
        return $this[ServiceBag::SERVICE_BAG_BASE_KEY];
    }


    /**
     * Retrieves the configuration bag
     *
     * @return ConfigurationBag
     */
    public function getConfigurationBag()
    {
        return $this[ConfigurationBag::CONFIGURATION_BAG_BASE_KEY];
    }

    /**
     * Retrieves the command bag
     *
     * @return CommandBag
     */
    public function getCommandBag()
    {
        return $this[CommandBag::COMMAND_BAG_BASE_KEY];
    }

    /**
     * Retrieves the event subscriber bag
     *
     * @return EventSubscriberBag
     */
    public function getEventSubscriberBag()
    {
        return $this[EventSubscriberBag::EVENT_SUBSCRIBER_BAG_BASE_KEY];
    }
}
