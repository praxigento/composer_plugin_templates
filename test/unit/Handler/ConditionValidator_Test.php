<?php
/**
 * Copyright (c) 2014, Praxigento
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and the following
 *      disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
 *      following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Composer\Plugin\Templates\Handler;

use Praxigento\Composer\Plugin\Templates\Config\Condition;

require_once(__DIR__ . '/../../../phpunit.bootstrap.php');

class ConditionValidator_Test extends \PHPUnit_Framework_TestCase {

    public function test_conditionVarIsNotInTemplateVars() {
        /* Prepare test mocks and data */
        $cond = new Condition();
        $cond->setVar('CONDITION');
        /* Create object and perform testing action. */
        $obj = new ConditionValidator();
        $res = $obj->isValid($cond, [ 'TEMPLATE' => 'value' ]);
        $this->assertFalse($res);
    }

    public function test_conditionEqual_varNotEqual() {
        /* Prepare test mocks and data */
        $cond = new Condition();
        $cond->setVar('VAR');
        $cond->setOperation(Condition::OPER_EQ);
        $cond->setValue('value');
        /* Create object and perform testing action. */
        $obj = new ConditionValidator();
        $res = $obj->isValid($cond, [ 'VAR' => 'not_value' ]);
        $this->assertFalse($res);
    }

    public function test_conditionEqual_varEqual() {
        /* Prepare test mocks and data */
        $cond = new Condition();
        $cond->setVar('VAR');
        $cond->setOperation(Condition::OPER_EQ);
        $cond->setValue('value');
        /* Create object and perform testing action. */
        $obj = new ConditionValidator();
        $res = $obj->isValid($cond, [ 'VAR' => 'value' ]);
        $this->assertTrue($res);
    }


    public function test_conditionNotEqual_varNotEqual() {
        /* Prepare test mocks and data */
        $cond = new Condition();
        $cond->setVar('VAR');
        $cond->setOperation(Condition::OPER_NEQ);
        $cond->setValue('value');
        /* Create object and perform testing action. */
        $obj = new ConditionValidator();
        $res = $obj->isValid($cond, [ 'VAR' => 'not_value' ]);
        $this->assertTrue($res);
    }

    public function test_conditionNotEqual_varEqual() {
        /* Prepare test mocks and data */
        $cond = new Condition();
        $cond->setVar('VAR');
        $cond->setOperation(Condition::OPER_NEQ);
        $cond->setValue('value');
        /* Create object and perform testing action. */
        $obj = new ConditionValidator();
        $res = $obj->isValid($cond, [ 'VAR' => 'value' ]);
        $this->assertFalse($res);
    }

}