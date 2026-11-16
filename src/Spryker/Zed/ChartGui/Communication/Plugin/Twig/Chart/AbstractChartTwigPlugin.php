<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart;

use Spryker\Shared\Chart\ChartPluginCollection\Exception\ChartPluginNotFoundException;
use Spryker\Shared\Chart\Dependency\Plugin\ChartPluginInterface;
use Spryker\Shared\Chart\Dependency\Plugin\TwigChartFunctionPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Twig\Environment;
use Twig\TwigFunction;

/**
 * @method \Spryker\Zed\ChartGui\Communication\ChartGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\ChartGui\ChartGuiConfig getConfig()
 */
abstract class AbstractChartTwigPlugin extends AbstractPlugin implements TwigChartFunctionPluginInterface
{
    /**
     * @api
     *
     * @var string
     */
    public const TWIG_FUNCTION_NAME = 'chart';

    protected const string CHART_KIND = 'chart';

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getName(): string
    {
        return static::TWIG_FUNCTION_NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return array<\Twig\TwigFunction>
     */
    public function getChartFunctions(): array
    {
        return [
            new TwigFunction(
                static::TWIG_FUNCTION_NAME,
                [$this, 'renderChart'],
                $this->getDefaultTwigOptions(),
            ),
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Twig\Environment $twig
     * @param string $chartPluginName
     * @param string|null $dataIdentifier
     *
     * @return string
     */
    public function renderChart(Environment $twig, $chartPluginName, $dataIdentifier = null): string
    {
        $context = $this->getChartContext($chartPluginName, $dataIdentifier);

        return $twig->render($this->getTemplateName(), $context);
    }

    protected function getTemplateName(): string
    {
        return '@ChartGui/Chart/chart.twig';
    }

    /**
     * @param string $chartPluginName
     * @param string|null $dataIdentifier
     *
     * @return array<string, mixed>
     */
    protected function getChartContext($chartPluginName, $dataIdentifier): array
    {
        $chartDataTransfer = $this->resolveChartPluginByName($chartPluginName)->getChartData($dataIdentifier);

        return [
            'data' => $chartDataTransfer,
            'chartKind' => static::CHART_KIND,
            'chartView' => $this->getFactory()->createChartViewResolver()->resolveChartView($chartDataTransfer, static::CHART_KIND),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultTwigOptions(): array
    {
        return [
            'is_safe' => ['html'],
            'needs_environment' => true,
        ];
    }

    /**
     * @param string $pluginName
     *
     * @throws \Spryker\Shared\Chart\ChartPluginCollection\Exception\ChartPluginNotFoundException
     *
     * @return \Spryker\Shared\Chart\Dependency\Plugin\ChartPluginInterface
     */
    protected function resolveChartPluginByName(string $pluginName): ChartPluginInterface
    {
        foreach ($this->getFactory()->getChartPlugins() as $chartPlugin) {
            if ($chartPlugin->getName() === $pluginName) {
                return $chartPlugin;
            }
        }

        throw new ChartPluginNotFoundException(sprintf('Chart plugin "%s" was not found', $pluginName));
    }
}
