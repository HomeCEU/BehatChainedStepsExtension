<?php

namespace BehatChainedStepsExtension;

use Behat\Behat\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class Extension implements ExtensionInterface
{

    /**
     * Loads a specific configuration.
     *
     * @param array            $config    Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/services'));
        $loader->load('core.xml');

        if (isset($config['tester']) && isset($config['tester']['step']) && isset($config['tester']['step']['class']))
        {
            $container->setParameter('behat.tester.step.class', $config['tester']['step']['class']);
        } else
        {
            $container->setParameter('behat.tester.step.class', $container->getParameter('behat.chained_steps_extension.tester.step.class'));
        }
    }

    /**
     * Setups configuration for current extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder->
                children()->
                scalarNode('trigger_hooks')->
                defaultValue(true)->
                end()->
                scalarNode('show_chained_steps')->
                defaultValue(true)->
                end()->
                arrayNode('tester')->
                children()->
                arrayNode('step')->
                children()->
                scalarNode('class')->
                defaultValue(null)->
                end()->
                end()->
                end()->
                end();
    }

    /**
     * Returns compiler passes used by this extension.
     *
     * @return array
     */
    public function getCompilerPasses()
    {
        return [];
    }

}
