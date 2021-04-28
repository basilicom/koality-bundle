<?php

namespace Basilicom\KoalityBundle\Checks;

use CustomerManagementFrameworkBundle\Maintenance\MaintenanceWorker;
use Leankoala\HealthFoundation\Check\Check;
use Leankoala\HealthFoundation\Check\Result;

class MaintenanceWorkerRunningCheck implements Check
{
    const IDENTIFIER = 'base:worker:running';

    /**
     * @return Result
     */
    public function run()
    {
        $worker = new ();

            $result = new Result(Result::STATUS_FAIL, 'Debug mode is ON');

        return $result;
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }
}
