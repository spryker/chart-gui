<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ChartGui\Communication\Plugin\Twig\Chart;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\ChartDataTraceTransfer;
use Generated\Shared\Transfer\ChartDataTransfer;
use Spryker\Shared\Chart\ChartPluginCollection\Exception\ChartPluginNotFoundException;
use Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart\AbstractChartTwigPlugin;
use Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart\BarChartTwigPlugin;
use Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart\ChartTwigPlugin;
use Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart\LineChartTwigPlugin;
use Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart\PieChartTwigPlugin;
use SprykerTest\Zed\ChartGui\ChartGuiCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ChartGui
 * @group Communication
 * @group Plugin
 * @group Twig
 * @group Chart
 * @group ChartTwigPluginTest
 * Add your own group annotations below this line
 */
class ChartTwigPluginTest extends Unit
{
    protected const string CHART_PLUGIN_NAME = 'test-chart';

    protected const string DATA_IDENTIFIER = 'test-chart-key';

    protected const string PATTERN_TEMPLATE_ATTRIBUTE = '/data-chart-template="(%s)"/';

    protected const string STYLESHEET_TAG = '<link rel="stylesheet" href="css/spryker-zed-chart-gui-main.css">';

    protected const string SCRIPT_TAG = '<script src="js/spryker-zed-chart-main.js" defer></script>';

    /**
     * @var list<string>
     */
    protected const array SHARED_TEMPLATE_NAMES = ['empty-state', 'error-state'];

    protected ChartGuiCommunicationTester $tester;

    public function testRenderChartPublishesOnlyTheFieldsTheChartScriptReads(): void
    {
        // Arrange
        $this->tester->haveChartPlugin(static::CHART_PLUGIN_NAME, $this->createChartDataTransfer());

        // Act
        $renderedChart = (new ChartTwigPlugin())->renderChart(
            $this->tester->createTwigEnvironment(),
            static::CHART_PLUGIN_NAME,
            static::DATA_IDENTIFIER,
        );

        // Assert
        $this->assertSame(
            [
                'kind' => 'chart',
                'data' => [
                    'key' => static::DATA_IDENTIFIER,
                    'title' => 'Orders by day',
                    'view' => 'daily-bar',
                    'traces' => [
                        [
                            'name' => 'Orders',
                            'type' => 'bar',
                            'labels' => ['2026-11-08', '2026-11-09'],
                            'values' => [3, 5],
                            'options' => [['orientation' => 'h']],
                        ],
                    ],
                ],
            ],
            $this->tester->getChartPayload($renderedChart),
        );
    }

    /**
     * @dataProvider provideChartTwigPluginsWithTheirKind
     */
    public function testRenderChartPassesTheKindOfThePlugin(AbstractChartTwigPlugin $chartTwigPlugin, string $expectedKind): void
    {
        // Arrange
        $this->tester->haveChartPlugin(static::CHART_PLUGIN_NAME, $this->createChartDataTransfer());

        // Act
        $renderedChart = $chartTwigPlugin->renderChart($this->tester->createTwigEnvironment(), static::CHART_PLUGIN_NAME);

        // Assert
        $this->assertSame($expectedKind, $this->tester->getChartPayload($renderedChart)['kind']);
    }

    /**
     * @return array<string, array<\Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart\AbstractChartTwigPlugin|string>>
     */
    public function provideChartTwigPluginsWithTheirKind(): array
    {
        return [
            'chart' => [new ChartTwigPlugin(), 'chart'],
            'bar chart' => [new BarChartTwigPlugin(), 'bar'],
            'line chart' => [new LineChartTwigPlugin(), 'line'],
            'pie chart' => [new PieChartTwigPlugin(), 'pie'],
        ];
    }

    /**
     * @dataProvider provideViewsWithTheirTemplates
     *
     * @param list<string> $expectedTemplateNames
     * @param list<string> $unexpectedTemplateNames
     */
    public function testRenderChartIncludesTheSharedTemplatesAndTheOnesOfTheView(
        string $view,
        array $expectedTemplateNames,
        array $unexpectedTemplateNames
    ): void {
        // Arrange
        $this->tester->haveChartPlugin(static::CHART_PLUGIN_NAME, $this->createChartDataTransfer()->setView($view));

        // Act
        $renderedChart = (new ChartTwigPlugin())->renderChart($this->tester->createTwigEnvironment(), static::CHART_PLUGIN_NAME);

        // Assert
        $this->assertStringContainsString('<h4 class="chart__title mb-1">Orders by day</h4>', $renderedChart);

        foreach ([...static::SHARED_TEMPLATE_NAMES, ...$expectedTemplateNames] as $templateName) {
            $this->assertStringContainsString(sprintf('data-chart-template="%s"', $templateName), $renderedChart);
        }

        foreach ($unexpectedTemplateNames as $templateName) {
            $this->assertStringNotContainsString(sprintf('data-chart-template="%s"', $templateName), $renderedChart);
        }
    }

    /**
     * @return array<string, array<string|list<string>>>
     */
    public function provideViewsWithTheirTemplates(): array
    {
        return [
            'cartesian' => [
                'cartesian',
                ['cartesian', 'cartesian-tooltip', 'cartesian-tooltip-row'],
                ['daily-bar', 'pie', 'order-status', 'ranking', 'legend', 'order-count'],
            ],
            'daily bar' => [
                'daily-bar',
                ['daily-bar', 'daily-subtitle', 'daily-summary', 'daily-tooltip', 'order-count'],
                ['cartesian', 'pie', 'order-status', 'ranking', 'legend'],
            ],
            'pie' => [
                'pie',
                ['pie', 'pie-center', 'pie-tooltip', 'legend', 'legend-row'],
                ['cartesian', 'daily-bar', 'order-status', 'ranking', 'order-count'],
            ],
            'order status, which draws the pie legend and tooltip' => [
                'order-status',
                ['order-status', 'order-status-subtitle', 'order-status-center', 'item-count', 'pie-tooltip', 'legend', 'legend-row'],
                ['cartesian', 'daily-bar', 'pie', 'pie-center', 'ranking', 'order-count'],
            ],
            'ranking' => [
                'ranking',
                ['ranking', 'ranking-subtitle', 'ranking-row'],
                ['cartesian', 'daily-bar', 'pie', 'order-status', 'legend', 'order-count'],
            ],
        ];
    }

    /**
     * @dataProvider provideChartsWithoutAViewAndTheResolvedView
     */
    public function testRenderChartResolvesTheViewFromTheKindOrTheFirstTraceWhenThePluginSetsNone(
        AbstractChartTwigPlugin $chartTwigPlugin,
        ChartDataTransfer $chartDataTransfer,
        string $expectedView
    ): void {
        // Arrange
        $this->tester->haveChartPlugin(static::CHART_PLUGIN_NAME, $chartDataTransfer->setTitle('Orders'));

        // Act
        $renderedChart = $chartTwigPlugin->renderChart($this->tester->createTwigEnvironment(), static::CHART_PLUGIN_NAME);

        // Assert
        $this->assertSame($expectedView, $this->tester->getChartPayload($renderedChart)['data']['view']);
        $this->assertStringContainsString(sprintf('data-chart-template="%s"', $expectedView), $renderedChart);
    }

    /**
     * @return array<string, array<\Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart\AbstractChartTwigPlugin|\Generated\Shared\Transfer\ChartDataTransfer|string>>
     */
    public function provideChartsWithoutAViewAndTheResolvedView(): array
    {
        return [
            'chart with a bar trace' => [new ChartTwigPlugin(), static::createChartDataTransferWithTrace('bar'), 'cartesian'],
            'chart with a line trace' => [new ChartTwigPlugin(), static::createChartDataTransferWithTrace('scatter'), 'cartesian'],
            'chart with a pie trace' => [new ChartTwigPlugin(), static::createChartDataTransferWithTrace('pie'), 'pie'],
            'chart with a pie trace not keyed from zero' => [
                new ChartTwigPlugin(),
                (new ChartDataTransfer())->setTraces(new ArrayObject([5 => static::createChartDataTraceTransfer('pie')])),
                'pie',
            ],
            'chart without traces' => [new ChartTwigPlugin(), new ChartDataTransfer(), 'cartesian'],
            'bar chart with a pie trace' => [new BarChartTwigPlugin(), static::createChartDataTransferWithTrace('pie'), 'cartesian'],
            'line chart with a bar trace' => [new LineChartTwigPlugin(), static::createChartDataTransferWithTrace('bar'), 'cartesian'],
            'pie chart with a bar trace' => [new PieChartTwigPlugin(), static::createChartDataTransferWithTrace('bar'), 'pie'],
        ];
    }

    public function testRenderChartKeepsOnlyTheSharedTemplatesForAViewWithoutATemplateFile(): void
    {
        // Arrange
        $this->tester->haveChartPlugin(static::CHART_PLUGIN_NAME, $this->createChartDataTransfer()->setView('funnel'));

        // Act
        $renderedChart = (new ChartTwigPlugin())->renderChart($this->tester->createTwigEnvironment(), static::CHART_PLUGIN_NAME);

        // Assert
        $this->assertSame('funnel', $this->tester->getChartPayload($renderedChart)['data']['view']);
        $this->assertSame(
            count(static::SHARED_TEMPLATE_NAMES),
            preg_match_all(sprintf(static::PATTERN_TEMPLATE_ATTRIBUTE, '[^"]+'), $renderedChart),
        );
        $this->assertSame(
            count(static::SHARED_TEMPLATE_NAMES),
            preg_match_all(sprintf(static::PATTERN_TEMPLATE_ATTRIBUTE, implode('|', static::SHARED_TEMPLATE_NAMES)), $renderedChart),
        );
    }

    public function testChartTemplatesBlockOverrideReplacesTheStrategyTemplates(): void
    {
        // Arrange
        $twigEnvironment = $this->tester->createTwigEnvironment();
        $chartTemplate = $twigEnvironment->createTemplate(
            "{% extends '@ChartGui/Chart/chart.twig' %}{% block chartTemplates %}<template data-chart-template=\"funnel\"></template>{% endblock %}",
        );

        // Act
        $renderedChart = $chartTemplate->render([
            'data' => $this->createChartDataTransfer()->setView('funnel'),
            'chartKind' => 'chart',
            'chartView' => 'funnel',
        ]);

        // Assert
        $this->assertSame(1, preg_match_all(sprintf(static::PATTERN_TEMPLATE_ATTRIBUTE, '[^"]+'), $renderedChart));
        $this->assertStringContainsString('data-chart-template="funnel"', $renderedChart);
    }

    public function testRenderChartFallsBackToTheTransferViewWhenTheContextHasNoChartView(): void
    {
        // Arrange
        $twigEnvironment = $this->tester->createTwigEnvironment();
        $twigEnvironment->enableStrictVariables();
        $chartTemplate = $twigEnvironment->load('@ChartGui/Chart/chart.twig');

        // Act
        $renderedChart = $chartTemplate->render([
            'data' => $this->createChartDataTransfer()->setView('ranking'),
            'chartKind' => 'chart',
        ]);

        // Assert
        $this->assertSame('ranking', $this->tester->getChartPayload($renderedChart)['data']['view']);
        $this->assertStringContainsString('data-chart-template="ranking-row"', $renderedChart);
    }

    public function testRenderChartRendersTheAssetsOncePerTwigEnvironment(): void
    {
        // Arrange
        $this->tester->haveChartPlugin(static::CHART_PLUGIN_NAME, $this->createChartDataTransfer());
        $chartTwigPlugin = new ChartTwigPlugin();
        $twigEnvironment = $this->tester->createTwigEnvironment();

        // Act
        $firstChart = $chartTwigPlugin->renderChart($twigEnvironment, static::CHART_PLUGIN_NAME, 'first');
        $secondChart = $chartTwigPlugin->renderChart($twigEnvironment, static::CHART_PLUGIN_NAME, 'second');
        $chartOnAnotherPage = $chartTwigPlugin->renderChart($this->tester->createTwigEnvironment(), static::CHART_PLUGIN_NAME, 'third');

        // Assert
        $this->assertSame(1, substr_count($firstChart, static::STYLESHEET_TAG));
        $this->assertSame(1, substr_count($firstChart, static::SCRIPT_TAG));
        $this->assertStringNotContainsString('<link', $secondChart);
        $this->assertStringNotContainsString('<script', $secondChart);
        $this->assertSame(1, substr_count($chartOnAnotherPage, static::STYLESHEET_TAG));
        $this->assertSame(1, substr_count($chartOnAnotherPage, static::SCRIPT_TAG));
    }

    public function testRenderChartThrowsWhenNoChartPluginHasTheName(): void
    {
        // Arrange
        $this->tester->haveChartPlugin(static::CHART_PLUGIN_NAME, $this->createChartDataTransfer());

        // Expect
        $this->expectException(ChartPluginNotFoundException::class);
        $this->expectExceptionMessage('Chart plugin "unknown-chart" was not found');

        // Act
        (new ChartTwigPlugin())->renderChart($this->tester->createTwigEnvironment(), 'unknown-chart');
    }

    public function testGetChartFunctionsRegistersTheFunctionUnderThePluginName(): void
    {
        // Act
        $twigFunctions = (new PieChartTwigPlugin())->getChartFunctions();

        // Assert
        $this->assertCount(1, $twigFunctions);
        $this->assertSame(PieChartTwigPlugin::TWIG_FUNCTION_NAME, $twigFunctions[0]->getName());
    }

    protected static function createChartDataTransferWithTrace(string $traceType): ChartDataTransfer
    {
        return (new ChartDataTransfer())->addTrace(static::createChartDataTraceTransfer($traceType));
    }

    protected static function createChartDataTraceTransfer(string $traceType): ChartDataTraceTransfer
    {
        return (new ChartDataTraceTransfer())->setName('Orders')->setType($traceType)->setLabels(['a'])->setValues([1]);
    }

    protected function createChartDataTransfer(): ChartDataTransfer
    {
        return (new ChartDataTransfer())
            ->setTitle('Orders by day')
            ->setView('daily-bar')
            ->addTrace(
                (new ChartDataTraceTransfer())
                    ->setName('Orders')
                    ->setType('bar')
                    ->setLabels(['2026-11-08', '2026-11-09'])
                    ->setValues([3, 5])
                    ->addOption(['orientation' => 'h']),
            );
    }
}
