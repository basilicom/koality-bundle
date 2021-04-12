<?php

namespace Basilicom\KoalityBundle\Checks;

use Leankoala\HealthFoundation\Check\Check;
use Leankoala\HealthFoundation\Check\Result;
use Leankoala\HealthFoundation\Check\MetricAwareResult;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;

class OrdersPerHourCheck implements Check
{
    const IDENTIFIER = 'base:sales:perHour';

    /**
     * @return Result
     */
    public function run()
    {
       $result = new MetricAwareResult(Result::STATUS_PASS, 'Count of orders during the last hour');
       $result->setMetric($this->getOrdersCountOfLastHour(), 'Orders', MetricAwareResult::METRIC_TYPE_NUMERIC);
       $result->setObservedValuePrecision(2);

       return $result;
    }

    public function getOrdersCountOfLastHour(): int
    {
        $orderManager = Factory::getInstance()->getOrderManager();
        $orderList = $orderManager->createOrderList();

        $query = $orderList->getQuery();
        $query->where('order.orderdate > ?', time() - 3600);

        return count($orderList);
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }
}
