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

use ConsoleApplication\DependencyInjection\Bag\ApplicationBag;
use ConsoleApplication\DependencyInjection\Bag\ConfigurationBag;
use ConsoleApplication\DependencyInjection\Bag\ConsoleBag;
use ConsoleApplication\DependencyInjection\Bag\ParameterBag;
use ConsoleApplication\DependencyInjection\Bag\ServiceBag;
use ConsoleApplication\DependencyInjection\Loader\FileLoader;
use ConsoleApplication\EventSubscriber\InitializeEventSubscriber;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Container extends \Pimple\Container
{
    /*------------------------------------------------------------------------*\
    | Constructor                                                              |
    \*------------------------------------------------------------------------*/

    /**
     * Creates a new container (method order is important)
     *
     * @param string $environment
     * @param string $appName
     * @param string $appVersion
     * @param string $directory
     */
    public function __construct($environment, $appName, $appVersion, $directory)
    {
        // Create bags and set default values.
        $this->createBags();
        $this->setDefaults($environment, $appName, $appVersion, $directory);

        // Load essential services and events (these are loaded first to enable overrides).
        $this->loadEssentialServices();
        $this->loadEssentialEventSubscribers();

        // Load configuration and parameters.
        $this->loadParameters();
        $this->loadConfigurations();

        // Load services.
        $this->loadServices();

        // Load EventSubscribers.
        $this->loadEventSubscribers();

        // Load commands.
        //$this->loadCommands();
    }

    /*------------------------------------------------------------------------*\
    | Public methods                                                           |
    \*------------------------------------------------------------------------*/

    //@TODO
    public function registerService(ServiceProviderInterface $serviceProvider)
    {
        //@TODO
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
     * @param string $environment
     * @param string $appName
     * @param string $appVersion
     * @param string $directory
     */
    private function setDefaults($environment, $appName, $appVersion, $directory)
    {
        // Application configurations.
        $applicationBag = $this->getApplicationBag();
        $applicationBag->set('charset', 'UTF-8');
        $applicationBag->set('env', $environment);
        $applicationBag->set('name', $appName);
        $applicationBag->set('version', $appVersion);

        // Useful directories.
        $applicationBag->set('dir.base', sprintf('%s/', realpath($directory)));
        $applicationBag->set('dir.app', sprintf('%s%s', $applicationBag->get('dir.base'), 'app/'));
        $applicationBag->set('dir.config', sprintf('%s%s', $applicationBag->get('dir.base'), 'app/config/'));
    }

    /**
     * Loads essential, always present services
     */
    private function loadEssentialServices()
    {
        // Get service bag.
        $serviceBag = $this->getServiceBag();

        // Logger (sent to null output by default).
        $serviceBag->setLogger(new ConsoleLogger(new NullOutput()));

        // Event dispatcher.
        $serviceBag->setDispatcher(new EventDispatcher());

        // File loader.
        $serviceBag->setFileLoader(new FileLoader());
    }

    /**
     * Loads essential event subscribers.
     */
    public function loadEssentialEventSubscribers()
    {
        // Initialize event subscriber.
        $this->registerEventSubscriber(new InitializeEventSubscriber());
    }

    /**
     * Load parameters
     */
    private function loadParameters()
    {
        // Get environment.
        $fileLoader = $this->getServiceBag()->getFileLoader();
        $applicationBag = $this->getApplicationBag();
        $env = $applicationBag->get('env');

        // Load general parameters.
        $fileLoader->loadFileToBag(
            $this->generateConfigFilePath('parameters.yml'),
            'parameters',
            $this->getParameterBag()
        );

        // Load environment parameters (ignore if file does not exist).
        $fileLoader->loadFileToBag(
            $this->generateConfigFilePath(sprintf('%s_%s.yml', 'parameters', $env)),
            'parameters',
            $this->getParameterBag(),
            false,
            true
        );
    }

    /**
     * Load configuration
     */
    private function loadConfigurations()
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
    private function loadServices()
    {
        //@TODO
    }

    /**
     * Loads event subscribers from configuration
     */
    private function loadEventSubscribers()
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
        return sprintf('%s%s', $this->getApplicationBag()->get('dir.config'), $filename);
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
}
