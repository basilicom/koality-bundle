<?php

namespace Basilicom\KoalityBundle\Checks;

use Leankoala\HealthFoundation\Check\Check;
use Leankoala\HealthFoundation\Check\MetricAwareResult;
use Leankoala\HealthFoundation\Check\Result;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;

class OrdersPerTimeIntervalCheck implements Check
{
    const IDENTIFIER = 'base:sales:perHour';

    private int $timeInterval;
    private int $threshold;

    public function init(int $timeInterval, int $threshold)
    {
        $this->timeInterval = $timeInterval;
        $this->threshold = $threshold;
    }

    /**
     * @return Result
     */
    public function run()
    {
        $ordersCount = $this->getOrdersCount();

        if ($ordersCount >= $this->threshold) {
            $result = new MetricAwareResult(
                Result::STATUS_PASS,
                'Count of orders during the specified time interval. Threshold: ' . $this->threshold
            );
            $result->setMetric($ordersCount, 'Orders', MetricAwareResult::METRIC_TYPE_NUMERIC);
            $result->setObservedValuePrecision(2);
        }
        if ($ordersCount < $this->threshold) {
            $result = new MetricAwareResult(
                Result::STATUS_FAIL,
                'Count of orders during the specified time interval. Threshold: ' . $this->threshold
            );
            $result->setMetric($ordersCount, 'Orders', MetricAwareResult::METRIC_TYPE_NUMERIC);
            $result->setObservedValuePrecision(2);
        }

        return $result;
    }

    public function getOrdersCount(): int
    {
        $orderManager = Factory::getInstance()->getOrderManager();
        $orderList = $orderManager->createOrderList();

        $query = $orderList->getQuery();
        $query->where('order.orderdate > ?', time() - 3600 * $this->timeInterval);

        return count($orderList);
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }
}
