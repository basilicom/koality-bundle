<?php

namespace Basilicom\KoalityBundle\Checks;

use Leankoala\HealthFoundation\Check\Check;
use Leankoala\HealthFoundation\Check\Result;
use Leankoala\HealthFoundation\Check\MetricAwareResult;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListItemInterface;
use \Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing\Filter\OrderDateTime;

class OrdersPerHourCheck implements Check
{
    const IDENTIFIER = 'base:sales:perHour';
    /**
     * Checks if the space left on device is sufficient
     *
     * @return Result
     */
    public function run()
    {
       $result = new MetricAwareResult(Result::STATUS_PASS, 'Count of orders during the last hour');
       $result->setMetric($this->getOrdersCountOfLastHour(), 'Orders', MetricAwareResult::METRIC_TYPE_NUMERIC);
       $result->setObservedValuePrecision(2);

       return $result;


    }

    public function getOrdersCountOfLastHour() {

        $orderManager = Factory::getInstance()->getOrderManager();
        $orderList = $orderManager->createOrderList();

        $orderList->setOrder( 'order.orderDate desc' );
        $orderList->setLimit( 10, 0 );




        // iterate
        foreach($orderList as $order)
        {
            /* @var \Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListItemInterface $order */
           // echo $order->getId();
           // echo PHP_EOL;
        }

        return count($orderList);
    }



    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }
}
