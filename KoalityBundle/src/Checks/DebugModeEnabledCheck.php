<?php

namespace Basilicom\KoalityBundle\Checks;

use Leankoala\HealthFoundation\Check\Check;
use Leankoala\HealthFoundation\Check\Result;

class DebugModeEnabledCheck implements Check
{
    const IDENTIFIER = 'base:mode:debug';

    /**
     * @return Result
     */
    public function run()
    {
        if (boolval(\Pimcore::inDebugMode()) === true) {
            $result = new Result(Result::STATUS_FAIL, 'Debug mode is ON');
        }
        if (boolval(\Pimcore::inDebugMode()) === false) {
            $result = new Result(Result::STATUS_PASS, 'Debug mode is OFF');
        }

        return $result;
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }
}
