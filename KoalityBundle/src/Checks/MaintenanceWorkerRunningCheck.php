<?php

namespace Basilicom\KoalityBundle\Checks;

use Leankoala\HealthFoundation\Check\Check;
use Leankoala\HealthFoundation\Check\Result;
use Pimcore\Maintenance\ExecutorInterface;

class MaintenanceWorkerRunningCheck implements Check
{
    const IDENTIFIER = 'base:worker:running';

    private ExecutorInterface $maintenanceExecutor;

    public function init(ExecutorInterface $maintenanceExecutor)
    {
        $this->maintenanceExecutor = $maintenanceExecutor;
    }

    /**
     * @return Result
     */
    public function run()
    {
        $maintenance_active = false;
        if ($lastExecution = $this->maintenanceExecutor->getLastExecution()) {
            if ((time() - $lastExecution) < 3660) { // maintenance script should run at least every hour + a little tolerance
                $maintenance_active = true;
            }
        }
        if ($maintenance_active === true) {
            $result = new Result(Result::STATUS_PASS, 'Maintenance Job was executed within the last hour');
        } else {
            $result = new Result(Result::STATUS_FAIL, 'Maintenance Job wasn\'t executed within the last hour');
        }

        return $result;
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }
}
