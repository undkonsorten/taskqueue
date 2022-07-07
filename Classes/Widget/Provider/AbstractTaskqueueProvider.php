<?php

namespace Undkonsorten\Taskqueue\Widget\Provider;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

abstract class AbstractTaskqueueProvider implements ChartDataProviderInterface
{
    /**
     * @var string
     */
    protected $title = 'Failed by tasks by entry date';

    /**
     * @var int
     */
    protected $width = 4;

    /**
     * @var int
     */
    protected $height = 4;

    /**
     * @var string
     */
    protected $datefield;

    /**
     * Number of days to gather information for.
     *
     * @var int
     */
    protected $days = 31;

    /**
     * @var int
     */
    protected $status = 0;

    public function __construct(int $days = 31)
    {
        $this->days = $days;
    }

    public function prepareData(): void
    {
    }

    /**
     *
     */
    protected function prepareChartData(): void
    {
        self::prepareChartData();

        $this->chartData = $this->getChartData();
    }

    /**
     * @return array
     */
    public function getChartData(): array
    {
        $period = 'lastMonth';

        $labels = [];
        $data = [];

        if ($period === 'lastWeek') {
            for ($daysBefore=7; $daysBefore--; $daysBefore>0) {
                $labels[] = date('d-m-Y', strtotime('-' . $daysBefore . ' day'));
                $startPeriod = strtotime('-' . $daysBefore . ' day 0:00:00');
                $endPeriod =  strtotime('-' . $daysBefore . ' day 23:59:59');

                $data[] = $this->getNumberOfTasksInPeriod($startPeriod, $endPeriod);
            }
        }

        if ($period === 'lastMonth') {
            for ($daysBefore=31; $daysBefore--; $daysBefore>0) {
                $labels[] = date('d-m-Y', strtotime('-' . $daysBefore . ' day'));
                $startPeriod = strtotime('-' . $daysBefore . ' day 0:00:00');
                $endPeriod =  strtotime('-' . $daysBefore . ' day 23:59:59');

                $data[] = $this->getNumberOfTasksInPeriod($startPeriod, $endPeriod);
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Number of tasks',
                    'borderColor' => WidgetApi::getDefaultChartColors()[0],
                    'fill' => false,
                    'data' => $data
                ]
            ]
        ];
    }

    protected function getNumberOfTasksInPeriod(int $start, int $end): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_taskqueue_domain_model_task');
        return (int)$queryBuilder
            ->select('*')
            ->from('tx_taskqueue_domain_model_task')
            ->where(
                $queryBuilder->expr()->eq('status', $this->status),
                $queryBuilder->expr()->gte($this->datefield, $start),
                $queryBuilder->expr()->lte($this->datefield, $end)
            )
            ->execute()
            ->rowCount();
    }

}
