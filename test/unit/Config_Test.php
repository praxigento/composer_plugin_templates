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

namespace Praxigento\Composer\Plugin\Templates;

use Composer\Script\ScriptEvents;

require_once(__DIR__ . '/../../phpunit.bootstrap.php');

class Config_Test extends \PHPUnit_Framework_TestCase {

    public function test_getEventsAvailable() {
        $events = Config::getEventsAvailable();
        $this->assertTrue(is_array($events));
    }

    public function test_getTemplatesForEvent() {
        $FILE = PRJ_ROOT . '/' . Main_Test::FILE_CONFIG_JSON_NVC;
        $eventName = ScriptEvents::POST_INSTALL_CMD;
        $config = new Config($FILE);
        $tmpls = $config->getTemplatesForEvent($eventName);
        $this->assertTrue(is_array($tmpls));
        $this->assertEquals(2, count($tmpls));
    }

    public function test_constructor() {
        $FILE = PRJ_ROOT . '/' . Main_Test::FILE_CONFIG_JSON_NVC;
        $config = new Config($FILE);
        $this->assertTrue(is_array($config->getVars()));
        $this->assertEquals(5, count($config->getVars()), "Wrong count of vars in the test data.");
        $tmpls = $config->getTemplates();
        $this->assertTrue(is_array($tmpls));
        $this->assertEquals(3, count($tmpls), "Wrong count of valid templates in the test data.");
        /** @var $t1 Config\Template */
        $t1 = $tmpls[0];
        $this->assertTrue(is_array($t1->getEvents()));
        $this->assertEquals(1, count($t1->getEvents()), "Wrong count of events for 'local.xml' template.");
        /** @var $t2 Config\Template */
        $t2 = $tmpls[1];
        $this->assertTrue(is_array($t2->getEvents()));
        $this->assertEquals(2, count($t2->getEvents()), "Wrong count of events for 'dump.sh' template.");
        /* validate events enabled */
        $events = $config->getEventsEnabled();
        $this->assertTrue(is_array($events));
        $this->assertEquals(3, count($events), "Wrong count of total enabled events for all templates.");
    }
}
