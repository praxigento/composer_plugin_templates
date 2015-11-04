<?php
/**
 * Condition entry for templates.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Composer\Plugin\Templates\Config;

class Condition {
    const OPER_EQ = '=';
    const OPER_NEQ = '!=';
    /**
     * Operation code for condition: [ '=' | '!=' ]
     * @var string
     */
    private $operation;
    /**
     * Value to use in the right part of the condition.
     * @var string
     */
    private $value;
    /**
     * Variable name to use in the left part of the condition.
     * @var string
     */
    private $var;

    /**
     * @return mixed
     */
    public function getOperation() {
        return $this->operation;
    }

    /**
     * @param mixed $val
     */
    public function setOperation($val) {
        $this->operation = $val;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $val
     */
    public function setValue($val) {
        $this->value = $val;
    }

    /**
     * @return mixed
     */
    public function getVar() {
        return $this->var;
    }

    /**
     * @param mixed $val
     */
    public function setVar($val) {
        $this->var = $val;
    }
}