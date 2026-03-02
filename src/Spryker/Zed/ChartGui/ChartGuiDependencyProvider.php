<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ChartGui;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\ChartGui\ChartGuiConfig getConfig()
 */
class ChartGuiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const PLUGIN_TWIG_CHART_GUI_FUNCTIONS = 'PLUGIN_TWIG_CHART_GUI_FUNCTIONS';

    /**
     * @var string
     */
    public const PLUGIN_CHART = 'PLUGIN_CHART';

    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = $this->addTwigChartFunctionPlugins($container);
        $container = $this->addChartPlugins($container);

        return $container;
    }

    protected function addTwigChartFunctionPlugins(Container $container): Container
    {
        $container->set(static::PLUGIN_TWIG_CHART_GUI_FUNCTIONS, function () {
            return $this->getTwigChartFunctionPlugins();
        });

        return $container;
    }

    protected function addChartPlugins(Container $container): Container
    {
        $container->set(static::PLUGIN_CHART, function () {
            return $this->getChartPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Shared\Chart\Dependency\Plugin\TwigChartFunctionPluginInterface>
     */
    protected function getTwigChartFunctionPlugins(): array
    {
        return [];
    }

    /**
     * @return array<\Spryker\Shared\Chart\Dependency\Plugin\ChartPluginInterface>
     */
    protected function getChartPlugins(): array
    {
        return [];
    }
}
