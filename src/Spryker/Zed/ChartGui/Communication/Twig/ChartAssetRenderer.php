<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ChartGui\Communication\Twig;

use Twig\Environment;

/**
 * "Rendered once" is page state, so it lives in the Twig environment every chart of the page renders with, not in this class.
 */
class ChartAssetRenderer implements ChartAssetRendererInterface
{
    protected const string TEMPLATE_CHART_ASSETS = '@ChartGui/Chart/_partials/chart-assets.twig';

    protected const string GLOBAL_CHART_ASSETS_RENDERED = 'chartGuiAssetsRendered';

    public function registerRenderState(Environment $twig): Environment
    {
        $twig->addGlobal(static::GLOBAL_CHART_ASSETS_RENDERED, false);

        return $twig;
    }

    public function render(Environment $twig): string
    {
        if ($twig->getGlobals()[static::GLOBAL_CHART_ASSETS_RENDERED] ?? false) {
            return '';
        }

        $chartAssets = $twig->render(static::TEMPLATE_CHART_ASSETS);
        $twig->addGlobal(static::GLOBAL_CHART_ASSETS_RENDERED, true);

        return $chartAssets;
    }
}
