<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ChartGui;

use Codeception\Actor;
use Generated\Shared\Transfer\ChartDataTransfer;
use ReflectionClass;
use Spryker\Service\Container\Container;
use Spryker\Shared\Chart\Dependency\Plugin\ChartPluginInterface;
use Spryker\Zed\ChartGui\ChartGuiConfig;
use Spryker\Zed\ChartGui\ChartGuiDependencyProvider;
use Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart\ChartGuiTwigPlugin;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class ChartGuiCommunicationTester extends Actor
{
    use _generated\ChartGuiCommunicationTesterActions;

    protected const string TWIG_NAMESPACE = 'ChartGui';

    protected const string PATTERN_CHART_PAYLOAD_ATTRIBUTE = '/\sdata-chart="([^"]*)"/';

    public function haveChartPlugin(string $chartPluginName, ChartDataTransfer $chartDataTransfer): void
    {
        $chartPlugin = new class ($chartPluginName, $chartDataTransfer) implements ChartPluginInterface {
            public function __construct(protected string $chartPluginName, protected ChartDataTransfer $chartDataTransfer)
            {
            }

            public function getName(): string
            {
                return $this->chartPluginName;
            }

            /**
             * @param string|null $dataIdentifier
             */
            public function getChartData($dataIdentifier = null): ChartDataTransfer
            {
                return $this->chartDataTransfer->setKey($dataIdentifier);
            }
        };

        $this->setDependency(ChartGuiDependencyProvider::PLUGIN_CHART, [$chartPlugin]);
    }

    public function createTwigEnvironment(): Environment
    {
        // One environment stands for one page. `ChartGuiTwigPlugin` extends it as the Zed application does, without the chart functions of the project wiring.
        $this->setDependency(ChartGuiDependencyProvider::PLUGIN_TWIG_CHART_GUI_FUNCTIONS, []);

        $filesystemLoader = new FilesystemLoader();
        $filesystemLoader->addPath($this->getPresentationDirectory(), static::TWIG_NAMESPACE);

        $twigEnvironment = new Environment($filesystemLoader);

        // `trans` and `assetsPath` belong to other modules of the Zed application; only the chart templates are under test.
        $twigEnvironment->addFilter(new TwigFilter('trans', fn (string $message): string => $message));
        $twigEnvironment->addFunction(new TwigFunction('assetsPath', fn (string $path): string => $path));

        return (new ChartGuiTwigPlugin())->extend($twigEnvironment, new Container());
    }

    /**
     * @return array<string, mixed>
     */
    public function getChartPayload(string $renderedChart): array
    {
        $this->assertSame(1, preg_match(static::PATTERN_CHART_PAYLOAD_ATTRIBUTE, $renderedChart, $matches), 'The rendered chart has no data-chart attribute.');

        return json_decode(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function getPresentationDirectory(): string
    {
        return sprintf('%s/Presentation', dirname((string)(new ReflectionClass(ChartGuiConfig::class))->getFileName()));
    }
}
