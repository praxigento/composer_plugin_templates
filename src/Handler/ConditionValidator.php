<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Composer\Plugin\Templates\Handler;

use Praxigento\Composer\Plugin\Templates\Config\Condition;

class ConditionValidator {
    /**
     * Validate condition using template variables.
     *
     * @param Condition $condition
     *
     * @return bool
     */
    public function isValid(Condition $condition, $vars) {
        $result = false;
        $name = $condition->getVar();
        $oper = $condition->getOperation();
        $value = (string)$condition->getValue();
        if(isset($vars[$name])) {
            $var = (string)$vars[$name];
            switch($oper) {
                case Condition::OPER_EQ:
                    $result = (strcmp($var, $value) == 0);
                    break;
                case Condition::OPER_NEQ:
                    $result = (strcmp($var, $value) != 0);
                    break;
            }
        }
        return $result;
    }
}