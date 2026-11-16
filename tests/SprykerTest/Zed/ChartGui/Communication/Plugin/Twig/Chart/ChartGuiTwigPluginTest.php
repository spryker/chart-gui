<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ChartGui\Communication\Plugin\Twig\Chart;

use Codeception\Test\Unit;
use Spryker\Service\Container\Container;
use Spryker\Zed\ChartGui\ChartGuiDependencyProvider;
use Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart\ChartGuiTwigPlugin;
use Spryker\Zed\ChartGui\Communication\Plugin\Twig\Chart\PieChartTwigPlugin;
use SprykerTest\Zed\ChartGui\ChartGuiCommunicationTester;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

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
 * @group ChartGuiTwigPluginTest
 * Add your own group annotations below this line
 */
class ChartGuiTwigPluginTest extends Unit
{
    protected ChartGuiCommunicationTester $tester;

    public function testExtendRegistersTheChartAssetsFunctionNextToTheFunctionsOfTheChartPlugins(): void
    {
        // Arrange
        $this->tester->setDependency(ChartGuiDependencyProvider::PLUGIN_TWIG_CHART_GUI_FUNCTIONS, [new PieChartTwigPlugin()]);

        // Act
        $twigEnvironment = (new ChartGuiTwigPlugin())->extend(new Environment(new ArrayLoader()), new Container());

        // Assert
        $this->assertNotNull($twigEnvironment->getFunction(ChartGuiTwigPlugin::TWIG_FUNCTION_NAME_CHART_ASSETS));
        $this->assertNotNull($twigEnvironment->getFunction(PieChartTwigPlugin::TWIG_FUNCTION_NAME));
    }

    public function testChartAssetsRendersTheStylesheetAndTheScriptOnTheFirstCallOnly(): void
    {
        // Arrange
        $twigEnvironment = $this->tester->createTwigEnvironment();

        // Act
        [$firstCall, $secondCall] = explode('|', $twigEnvironment->createTemplate('{{ chartAssets() }}|{{ chartAssets() }}')->render());

        // Assert
        $this->assertSame(1, substr_count($firstCall, '<link rel="stylesheet" href="css/spryker-zed-chart-gui-main.css">'));
        $this->assertSame(1, substr_count($firstCall, '<script src="js/spryker-zed-chart-main.js" defer></script>'));
        $this->assertSame('', $secondCall);
    }
}
