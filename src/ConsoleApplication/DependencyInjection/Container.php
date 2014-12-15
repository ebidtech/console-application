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
use ConsoleApplication\Bag\ContainerLessBag;
use ConsoleApplication\Bag\EventSubscriberBag;
use ConsoleApplication\Bag\ParameterBag;
use ConsoleApplication\Bag\ServiceBag;
use ConsoleApplication\Console\Command\BaseCommand;
use ConsoleApplication\DependencyInjection\Loader\FileLoader;
use ConsoleApplication\Exception\ConfigurationException;
use ConsoleApplication\Exception\LogicException;
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
            $this->initialized = true;
        }
    }

    /**
     * Registers a new service
     *
     * @param ServiceProviderInterface $serviceProvider
     * @param string                   $name
     */
    public function registerService(ServiceProviderInterface $serviceProvider, $name)
    {
        $this->getServiceBag()->set($name, $serviceProvider->register($this));
    }

    /**
     * Registers a new event subscriber
     *
     * @param EventSubscriberInterface $eventSubscriber
     * @param string                   $name
     */
    public function registerEventSubscriber(EventSubscriberInterface $eventSubscriber, $name)
    {
        $this->getEventSubscriberBag()->set($name, $eventSubscriber->register($this));
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
        $this[CommandBag::COMMAND_BAG_BASE_KEY] = new CommandBag($this);
        $this[EventSubscriberBag::EVENT_SUBSCRIBER_BAG_BASE_KEY] = new EventSubscriberBag($this);
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
        // Load file.
        $bag = new ContainerLessBag();
        $fileLoader = $this->getServiceBag()->getFileLoader();
        $filename = $this->generateConfigFilePath('service_providers.yml');
        $fileLoader->loadFileToBag($filename, 'service_providers', $bag);

        // Resolve parameters.
        $fileLoader->resolveParameters($bag, $this->getParameterBag());

        // Instantiate services and load then to the container.
        foreach ($bag->values() as $name => $values) {
            // Check if class is set (null counts as unset).
            if (!isset($values['class'])) {
                throw ConfigurationException::configurationAttributeNotFoundException($filename, $name, 'class');
            }
            $class = $values['class'];
            $service = $this->instantiateClass($class, isset($values['arguments']) ? $values['arguments'] : array());

            if (!($service instanceof ServiceProviderInterface)) {
                throw LogicException::mustImplementInterfaceException('ServiceProviderInterface', $class);
            }

            // Register the service.
            $this->registerService($service, $name);
        }
    }

    /**
     * Loads event subscribers from configuration
     */
    private function loadEventSubscribersFile()
    {
        // Load file.
        $bag = new ContainerLessBag();
        $fileLoader = $this->getServiceBag()->getFileLoader();
        $filename = $this->generateConfigFilePath('event_subscribers.yml');
        $fileLoader->loadFileToBag($filename, 'event_subscribers', $bag);

        // Resolve parameters.
        $fileLoader->resolveParameters($bag, $this->getParameterBag());

        // Instantiate services and load then to the container.
        foreach ($bag->values() as $name => $values) {
            // Check if class is set (null counts as unset).
            if (!isset($values['class'])) {
                throw ConfigurationException::configurationAttributeNotFoundException($filename, $name, 'class');
            }
            $class = $values['class'];
            $eventSubscriber = $this->instantiateClass(
                $class,
                isset($values['arguments']) ? $values['arguments'] : array()
            );

            if (!($eventSubscriber instanceof EventSubscriberInterface)) {
                throw LogicException::mustImplementInterfaceException('EventSubscriberInterface', $class);
            }

            // Register the event subscriber.
            $this->registerEventSubscriber($eventSubscriber, $name);
        }
    }

    /**
     * Loads commands from configuration
     */
    private function loadCommandsFile()
    {
        // Load file.
        $bag = new ContainerLessBag();
        $commandBag = $this->getCommandBag();
        $fileLoader = $this->getServiceBag()->getFileLoader();
        $filename = $this->generateConfigFilePath('commands.yml');
        $fileLoader->loadFileToBag($filename, 'commands', $bag);

        // Resolve parameters.
        $fileLoader->resolveParameters($bag, $this->getParameterBag());

        // Instantiate services and load then to the container.
        foreach ($bag->values() as $name => $values) {
            // Check if class is set (null counts as unset).
            if (!isset($values['class'])) {
                throw ConfigurationException::configurationAttributeNotFoundException($filename, $name, 'class');
            }
            $class = $values['class'];

            // Override command name if defined.
            $name = isset($values['name']) ? $values['name'] : $name;
            $command = $this->instantiateClass($class, array($name));

            // Check if correct class.
            if (!($command instanceof BaseCommand)) {
                throw LogicException::mustExtendClassException('BaseCommand', $class);
            }

            // Add description.
            if (isset($values['description'])) {
                $command->setDescription($values['description']);
            }

            // Register the command.
            $commandBag->set($name, $command);
        }
    }

    /**
     * Generate a configuration file path
     *
     * @param string $filename
     *
     * @return string
     *
     * @throws LogicException
     */
    private function generateConfigFilePath($filename)
    {
        return sprintf('%s%s', $this->getApplicationBag()->getDirectory('config'), $filename);
    }

    /**
     * Instantiates an object
     *
     * @param string $class
     * @param array  $arguments
     *
     * @return object
     */
    private function instantiateClass($class, $arguments = array())
    {
        // Checks if the class exists.
        if (!class_exists($class)) {
            throw LogicException::classNotFoundException($class);
        }

        // Get class reflection.
        $reflection = new \ReflectionClass($class);

        // Checks if the class is instantiable.
        if (!($reflection->isInstantiable())) {
            throw LogicException::classNotInstantiableException($class);
        }

        // Checks if the constructor exists and takes the right number of parameters.
        if ($reflection->hasMethod('__construct')) {
            $method = $reflection->getMethod('__construct');

            if ($method->getNumberOfRequiredParameters() > count($arguments)) {
                throw LogicException::invalidNumberOfParametersException(
                    $class,
                    '__construct()',
                    $method->getNumberOfRequiredParameters()
                );
            }
        }

        // Instantiate class and return the object.
        return $reflection->newInstanceArgs($arguments);
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
