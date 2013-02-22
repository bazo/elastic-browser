<?php

namespace Extensions;

use Nette\Framework;

/**
 * Console service.
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
class ConsoleExtension extends \Nette\Config\CompilerExtension {

    private $defaults = [
        'catchExceptions' => true
    ];

    /**
     * Processes configuration data
     *
     * @return void
     */
    public function loadConfiguration() {

        $config = $this->getConfig($this->defaults);

        $container = $this->getContainerBuilder();

        // console application
        $container->addDefinition($this->prefix('console'))
                  ->setClass('Symfony\Component\Console\Application')
                  ->setFactory('Extensions\ConsoleExtension::createConsole', array('@container', $config))
                  ->setAutowired(false);

        // aliases
        $container->addDefinition('console')
                  ->setClass('Symfony\Component\Console\Application')
                  ->setFactory('@container::getService', array($this->prefix('console')));

        $builder = $this->getContainerBuilder();

        $this->compiler->parseServices($builder, $this->loadFromFile(__DIR__. '/../config/console.neon'), 'console');

    }

    /**
     * @param \Nette\DI\Container
     * @param \Symfony\Component\Console\Helper\HelperSet
     * @return \Symfony\Component\Console\Application
     */
    public static function createConsole(\Nette\DI\Container $container, $config, \Symfony\Component\Console\Helper\HelperSet $helperSet = null) {
        $console = new \Symfony\Component\Console\Application(Framework::NAME . " Command Line Interface", Framework::VERSION);

        if (!$helperSet) {
            $helperSet = new \Symfony\Component\Console\Helper\HelperSet;

            foreach (array_keys($container->findByTag('consoleHelper')) as $helperName) {
                $helperSet->set($container->getService($helperName), $helperName);
            }
        }

        $console->setHelperSet($helperSet);
        $console->setCatchExceptions(false);

        $commands = array();
        foreach (array_keys($container->findByTag('consoleCommand')) as $name) {
            $commands[] = $container->getService($name);
        }
        $console->addCommands($commands);
        $console->setCatchExceptions($config['catchExceptions']);
        return $console;
    }
}

