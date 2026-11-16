<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ChartGui\Communication\Twig;

use Twig\Environment;

interface ChartAssetRendererInterface
{
    /**
     * Specification:
     * - Registers the Twig global that records whether the chart assets were rendered in this environment.
     * - Must run while the environment is extended, before its first render.
     */
    public function registerRenderState(Environment $twig): Environment;

    /**
     * Specification:
     * - Renders the chart stylesheet and script tags when the environment has not rendered them yet.
     * - Returns an empty string otherwise.
     */
    public function render(Environment $twig): string;
}
