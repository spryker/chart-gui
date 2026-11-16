<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ChartGui\Communication\Resolver;

use Generated\Shared\Transfer\ChartDataTransfer;

interface ChartViewResolverInterface
{
    /**
     * Specification:
     * - Returns `ChartDataTransfer.view` when it is set.
     * - Otherwise returns `pie` for a pie series and `cartesian` for any other series.
     * - The series is the chart kind, or the type of the first trace for the `chart` kind.
     */
    public function resolveChartView(ChartDataTransfer $chartDataTransfer, string $chartKind): string;
}
