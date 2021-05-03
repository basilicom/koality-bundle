<?php

namespace Basilicom\KoalityBundle\Checks;

use Leankoala\HealthFoundation\Check\Check;
use Leankoala\HealthFoundation\Check\MetricAwareResult;
use Leankoala\HealthFoundation\Check\Result;

class NewCartsPerTimeIntervalCheck implements Check
{
    const IDENTIFIER = 'base:carts:per_time_interval';

    private int $timeInterval;

    public function init(int $timeInterval)
    {
        $this->timeInterval = $timeInterval;
    }

    /**
     * @return Result
     */
    public function run()
    {
        $timeInterval = time() - 3600 * $this->timeInterval;

        $db = \Pimcore\Db::get();
        $queryResult = $db->fetchAll('
            SELECT COUNT(*) 
            FROM ecommerceframework_cart 
            WHERE creationDateTimestamp >= ' . $timeInterval);

        if (!empty($queryResult)) {
            $countOfCarts = $queryResult[0]['COUNT(*)'];
        }

        $result = new MetricAwareResult(
            Result::STATUS_PASS,
            'Count of new carts during the last ' . $this->timeInterval . ' hour(s)');

        $result->setMetric( $countOfCarts, 'Carts', MetricAwareResult::METRIC_TYPE_NUMERIC);
        $result->setObservedValuePrecision(2);

        return $result;
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }
}
