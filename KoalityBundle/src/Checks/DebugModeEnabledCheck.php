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
        elseif (boolval(\Pimcore::inDebugMode()) === false) {
            $result = new Result(Result::STATUS_PASS, 'Debug mode is OFF');
        }
        else {
            $result = new Result(Result::STATUS_WARN, 'Debug mode state could not be determined');
        }

        return $result;
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }
}
