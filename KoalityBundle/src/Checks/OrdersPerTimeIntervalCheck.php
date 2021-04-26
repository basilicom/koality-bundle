<?php

namespace Basilicom\KoalityBundle\Checks;

use Leankoala\HealthFoundation\Check\Check;
use Leankoala\HealthFoundation\Check\Result;
use Leankoala\HealthFoundation\Check\MetricAwareResult;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;

class OrdersPerTimeIntervalCheck implements Check
{
    const IDENTIFIER = 'base:sales:perHour';

    private int $timeInterval;

    public function init(int $timeInterval) {
        $this->timeInterval = $timeInterval;
    }

    /**
     * @return Result
     */
    public function run()
    {
       $result = new MetricAwareResult(Result::STATUS_PASS, 'Count of orders during the specified time interval');
       $result->setMetric($this->getOrdersCount(), 'Orders', MetricAwareResult::METRIC_TYPE_NUMERIC);
       $result->setObservedValuePrecision(2);

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
