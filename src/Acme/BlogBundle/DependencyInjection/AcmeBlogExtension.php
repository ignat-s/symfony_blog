<?php

namespace Acme\BlogBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AcmeBlogExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->loadBlog($configs, $container);
        $this->loadMenu($container);
    }

    private function loadBlog(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $doctrineType = $config['doctrine']['type']; // "doctrine_orm" or "doctrine_mongodb"
        $doctrineManagerName = $config['doctrine']['manager_name'];

        $container->setParameter('acme_blog.doctrine.manager_name', $doctrineManagerName);

        $blogLoader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $blogLoader->load('blog.xml');

        $blogDoctrineLoader = new Loader\XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config/blog')
        );
        $blogDoctrineLoader->load($doctrineType . '.xml');
    }

    private function loadMenu(ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('menu.yml');
    }
}
