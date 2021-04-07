<?php

namespace Leankoala\HealthFoundation\Check\Device;

use Leankoala\HealthFoundation\Check\Check;
use Leankoala\HealthFoundation\Check\Result;

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
       $result = new Result(Result::STATUS_PASS, 'Count of orders during the last hour');
       $result->addAttribute('Orders within last hour', 1);

       return $result;


    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }
}
