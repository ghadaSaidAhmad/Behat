<?php

/*
 * This file is part of the Behat Testwork.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Testwork\Subject\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ServiceProcessor;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Testwork subject extension.
 *
 * Extends testwork with test subject services.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class SubjectExtension implements Extension
{
    /*
     * Available services
     */
    const FINDER_ID = 'subject.finder';

    /*
     * Available extension points
     */
    const LOCATOR_TAG = 'subject.locator';

    /**
     * @var ServiceProcessor
     */
    private $processor;

    /**
     * Initializes extension.
     *
     * @param null|ServiceProcessor $processor
     */
    public function __construct(ServiceProcessor $processor = null)
    {
        $this->processor = $processor ?: new ServiceProcessor();
    }

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'subjects';
    }

    /**
     * Setups configuration for the extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadFinder($container);
    }

    /**
     * Processes shared container after all extensions loaded.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->processLocators($container);
    }

    /**
     * Loads subject finder.
     *
     * @param ContainerBuilder $container
     */
    protected function loadFinder(ContainerBuilder $container)
    {
        $definition = new Definition('Behat\Testwork\Subject\SubjectFinder');
        $container->setDefinition(self::FINDER_ID, $definition);
    }

    /**
     * Processes subject locators.
     *
     * @param ContainerBuilder $container
     */
    protected function processLocators(ContainerBuilder $container)
    {
        $references = $this->processor->findAndSortTaggedServices($container, self::LOCATOR_TAG);
        $definition = $container->getDefinition(self::FINDER_ID);

        foreach ($references as $reference) {
            $definition->addMethodCall('registerSubjectLocator', array($reference));
        }
    }
}
