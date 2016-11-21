<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2015 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 * @author Elcodi Team <tech@elcodi.com>
 */

namespace Elcodi\Component\Plugin\Loader;

use Elcodi\Component\Plugin\Repository\PluginRepository;
use RuntimeException;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Elcodi\Component\Plugin\Interfaces\PluginInterface as PluginBundleInterface;

use Elcodi\Component\Plugin\Entity\Plugin;

/**
 * Class RouterLoader
 */
class RouterLoader extends Loader
{
    /**
     * @var boolean
     *
     * Route is loaded
     */
    private $loaded = false;

    /**
     * @var KernelInterface
     *
     * Kernel
     */
    private $kernel;

    /**
     * Construct
     *
     * @param KernelInterface $kernel Kernel
     */
    public function __construct(
        KernelInterface $kernel
    ) {
        $this->kernel = $kernel;
    }

    /**
     * Loads a resource.
     *
     * @param mixed  $resource The resource
     * @param string $type     The resource type
     *
     * @return RouteCollection
     *
     * @throws RuntimeException Loader is added twice
     */
    public function load($resource, $type = null)
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $routes = new RouteCollection();

        $routes->addCollection(
            $this->addPluginsRoutesCollection()
        );

        $this->loaded = true;

        return $routes;
    }

    /**
     * Return route collection for injected plugins
     *
     * @return RouteCollection Collection generated
     */
    protected function addPluginsRoutesCollection()
    {
        $routes = new RouteCollection();
        $bundles = $this->kernel->getBundles();
        foreach ($bundles as $bundle) {
            if ($bundle instanceof PluginBundleInterface) {
                $routes->addCollection(
                    $this->addPluginRoutesCollection($bundle)
                );
            }
        }
        return $routes;
    }

    /**
     * Return route collection for injected plugins
     *
     * @return RouteCollection Collection generated
     */
    protected function addPluginRoutesCollection(BundleInterface $pluginBundle)
    {
        $routes = new RouteCollection();
        $routingFilePath = '/Resources/config/routing.yml';
        $resourcePath = $pluginBundle->getPath() . $routingFilePath;
        $type = 'yaml';

        if (file_exists($resourcePath)) {
            $routes->addCollection(
                $this
                    ->import(
                        '@' . $pluginBundle->getName() . $routingFilePath,
                        $type
                    )
            );
        }

        return $routes;
    }

    private function getPluginBundles()
    {
        $bundles = $this->kernel->getBundles();
        foreach ($bundles as $bundle) {

        }
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false
     *              otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'elcodi.routes.plugins' === $type;
    }
}
