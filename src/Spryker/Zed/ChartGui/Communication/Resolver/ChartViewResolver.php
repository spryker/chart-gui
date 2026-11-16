<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ChartGui\Communication\Resolver;

use Generated\Shared\Transfer\ChartDataTransfer;
use Spryker\Shared\Chart\ChartConfig;

class ChartViewResolver implements ChartViewResolverInterface
{
    /**
     * The `chart` Twig function draws each trace by its own type; `barChart`, `lineChart` and `pieChart` draw every trace as their kind.
     */
    protected const string CHART_KIND_BY_TRACE_TYPE = 'chart';

    protected const string CHART_VIEW_CARTESIAN = 'cartesian';

    protected const string CHART_VIEW_PIE = 'pie';

    public function resolveChartView(ChartDataTransfer $chartDataTransfer, string $chartKind): string
    {
        if ($chartDataTransfer->getView()) {
            return $chartDataTransfer->getView();
        }

        $seriesType = $chartKind === static::CHART_KIND_BY_TRACE_TYPE ? $this->findFirstTraceType($chartDataTransfer) : $chartKind;

        return $seriesType === ChartConfig::CHART_TYPE_PIE ? static::CHART_VIEW_PIE : static::CHART_VIEW_CARTESIAN;
    }

    // Iterated, not read at index 0: a trace collection built with setTraces() can be keyed from any number.
    protected function findFirstTraceType(ChartDataTransfer $chartDataTransfer): ?string
    {
        foreach ($chartDataTransfer->getTraces() as $chartDataTraceTransfer) {
            return $chartDataTraceTransfer->getType();
        }

        return null;
    }
}
