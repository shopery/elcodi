<?php

namespace Elcodi\Component\Plugin\Factory;

use Elcodi\Component\Plugin\Entity\Plugin;
use Elcodi\Component\Plugin\Entity\PluginConfiguration;

class PluginFactory
{
    /**
     * Return new plugin instance
     *
     * @param string              $namespace     Namespace
     * @param string              $type          Type
     * @param string              $category      Plugin category
     * @param PluginConfiguration $configuration Configuration
     * @param boolean             $enabled       If the plugin should be enabled
     *
     * @return Plugin
     */
    public function create(
        $namespace,
        $type,
        $category,
        PluginConfiguration $configuration,
        $enabled
    ) {
        return new Plugin(
            $namespace,
            $type,
            $category,
            $configuration,
            $enabled
        );
    }
}
