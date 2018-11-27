<?php

namespace Dontdrinkandroot\AppIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class DdrAppIntegrationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        /** @var array $bundles */
        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('ApiPlatformBundle', $bundles)) {
            $loader->load('services_api_platform.yaml');
        }

        $container->setParameter('ddr_angular_integration.base_href', $config['base_href']);
        $container->setParameter('ddr_angular_integration.angular_directory', $config['angular_directory']);
        $container->setParameter('ddr_angular_integration.angular_src_directory', $config['angular_src_directory']);
        $container->setParameter('ddr_angular_integration.angular_path', $config['angular_path']);
        $container->setParameter('ddr_angular_integration.api_path', $config['api_path']);
        $container->setParameter('ddr_angular_integration.name', $config['name']);
        $container->setParameter('ddr_angular_integration.short_name', $config['short_name']);
        $container->setParameter('ddr_angular_integration.theme_color', $config['theme_color']);
        $container->setParameter('ddr_angular_integration.background_color', $config['background_color']);
        $container->setParameter('ddr_angular_integration.external_styles', $config['external_styles']);
        $container->setParameter('ddr_angular_integration.package_manager', $config['package_manager']);
    }
}
