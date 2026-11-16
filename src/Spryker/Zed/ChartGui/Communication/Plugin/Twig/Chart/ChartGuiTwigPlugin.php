<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart;

use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\TwigExtension\Dependency\Plugin\TwigPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Twig\Environment;
use Twig\TwigFunction;

/**
 * @method \Spryker\Zed\ChartGui\Communication\ChartGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\ChartGui\ChartGuiConfig getConfig()
 * @method \Spryker\Zed\ChartGui\Business\ChartGuiFacadeInterface getFacade()
 */
class ChartGuiTwigPlugin extends AbstractPlugin implements TwigPluginInterface
{
    /**
     * @api
     *
     * @var string
     */
    public const string TWIG_FUNCTION_NAME_CHART_ASSETS = 'chartAssets';

    /**
     * {@inheritDoc}
     * - Registers the `chartAssets` function, which renders the chart stylesheet and script once per Twig environment.
     * - Registers the `chartGuiAssetsRendered` Twig global that `chartAssets` keeps its state in.
     * - Registers the functions of the `TwigChartFunctionPluginInterface` plugins.
     *
     * @api
     *
     * @param \Twig\Environment $twig
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Twig\Environment
     */
    public function extend(Environment $twig, ContainerInterface $container): Environment
    {
        $twig = $this->getFactory()->createChartAssetRenderer()->registerRenderState($twig);

        return $this->registerChartTwigFunctions($twig);
    }

    protected function registerChartTwigFunctions(Environment $twig): Environment
    {
        foreach ($this->getChartTwigFunctions() as $function) {
            $twig->addFunction($function);
        }

        return $twig;
    }

    /**
     * @return array<\Twig\TwigFunction>
     */
    protected function getChartTwigFunctions(): array
    {
        $functions = [$this->createChartAssetsTwigFunction()];
        foreach ($this->getFactory()->getTwigChartFunctionPlugins() as $twigFunctionPlugin) {
            $functions = array_merge($functions, $twigFunctionPlugin->getChartFunctions());
        }

        return $functions;
    }

    protected function createChartAssetsTwigFunction(): TwigFunction
    {
        return new TwigFunction(
            static::TWIG_FUNCTION_NAME_CHART_ASSETS,
            [$this->getFactory()->createChartAssetRenderer(), 'render'],
            [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ],
        );
    }
}
