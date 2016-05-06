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

class Main_Test extends \PHPUnit_Framework_TestCase
{
    const CLAZZ = 'Praxigento\Composer\Plugin\Templates\Main';
    const FILE_CONFIG_JSON_INVALID = 'test/data/invalid.json';
    const FILE_CONFIG_JSON_NVC = 'test/data/templates_not_under_vc.json';
    const FILE_CONFIG_JSON_VC = 'test/data/templates_under_vc.json';
    const FILE_TMPL_DST = 'test/bin/dump_db/dump.sh';
    const FILE_TMPL_SRC = 'test/tmpl/dump.sh';
    /** @var  \Mockery\MockInterface */
    private $mComposer;
    /** @var  \Mockery\MockInterface */
    private $mIo;
    /** @var  \Mockery\MockInterface */
    private $mPackage;
    /** @var  Main */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mComposer = \Mockery::mock(\Composer\Composer::class);
        $this->mPackage = \Mockery::mock(\Composer\Package\RootPackageInterface::class);
        $this->mIo = \Mockery::mock(\Composer\IO\IOInterface::class);
        /** common setup for mocks */
        $this->mComposer
            ->shouldReceive('getPackage')
            ->andReturn($this->mPackage);
        $this->mIo->shouldReceive('write');
        /** create object to test */
        $this->obj = new Main();
    }


    public function test_activate_withExtra()
    {
        /** === Test Data === */
        $FILENAME = PRJ_ROOT . '/' . self::FILE_CONFIG_JSON_NVC;
        /** === Setup Mocks === */
        $this->mPackage
            ->shouldReceive('getExtra')
            ->andReturn([Main::EXTRA_PARAM => $FILENAME]);
        /** === Call and asserts  === */
        $this->obj->activate($this->mComposer, $this->mIo);
        $this->assertEquals($FILENAME, $this->obj->getConfigFileNames()[0]);
    }

    public function test_activate_withExtra_asArray()
    {
        /** === Test Data === */
        $FILENAME_1 = PRJ_ROOT . '/' . self::FILE_CONFIG_JSON_NVC;
        $FILENAME_2 = PRJ_ROOT . '/' . self::FILE_CONFIG_JSON_VC;
        /** === Setup Mocks === */
        $this->mPackage
            ->shouldReceive('getExtra')
            ->andReturn([Main::EXTRA_PARAM => [$FILENAME_1, $FILENAME_2]]);
        /** === Call and asserts  === */
        $this->obj->activate($this->mComposer, $this->mIo);
        $this->assertEquals($FILENAME_1, $this->obj->getConfigFileNames()[0]);
        $this->assertEquals($FILENAME_2, $this->obj->getConfigFileNames()[1]);
    }

    public function test_activate_withExtra_wrongFile()
    {
        /** === Test Data === */
        $FILENAME = PRJ_ROOT . '/' . self::FILE_CONFIG_JSON_NVC;
        /** === Setup Mocks === */
        $this->mPackage
            ->shouldReceive('getExtra')
            ->andReturn([Main::EXTRA_PARAM => $FILENAME . '_missedFile']);
        $this->mIo
            ->shouldReceive('writeError')->once();
        /** === Call and asserts  === */
        $this->obj->activate($this->mComposer, $this->mIo);
    }

    public function test_activate_withInvalidJson()
    {
        /** === Test Data === */
        $FILENAME = PRJ_ROOT . '/' . self::FILE_CONFIG_JSON_INVALID;
        /** === Setup Mocks === */
        $this->mPackage
            ->shouldReceive('getExtra')
            ->andReturn([Main::EXTRA_PARAM => $FILENAME]);
        // $io->writeError(__CLASS__ . ": <error>Cannot read valid JSON from configuration file '$one'. Plugin will be disabled.</error>", true);
        $expected = self::CLAZZ . ": <error>Cannot read valid JSON from configuration file '$FILENAME'. Plugin will be disabled.</error>";
        $this->mIo
            ->shouldReceive('writeError')->once()
            ->with($expected, true);
        /** === Call and asserts  === */
        $this->obj->activate($this->mComposer, $this->mIo);
    }

    public function test_activate_withoutExtra()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        $this->mPackage
            ->shouldReceive('getExtra')
            ->andReturn([]);
        $this->mIo
            ->shouldReceive('writeError')->once();
        /** === Call and asserts  === */
        $this->obj->activate($this->mComposer, $this->mIo);
        $this->assertNull($this->obj->getConfigFileNames());
    }

    public function test_getSubscribedEvents()
    {
        /** === Call and asserts  === */
        $events = Main::getSubscribedEvents();
        $refl = new \ReflectionClass(\Composer\Script\ScriptEvents::class);
        foreach ($refl->getConstants() as $one) {
            $this->assertArrayHasKey(ScriptEvents::POST_INSTALL_CMD, $events);
        }
    }

    public function test_onEvent()
    {
        /** === Test Data === */
        $eventName = ScriptEvents::POST_INSTALL_CMD;
        $template = new Config\Template();
        $template->setSource(self::FILE_TMPL_SRC);
        $template->setDestination(self::FILE_TMPL_DST);
        $events = [$eventName];
        $template->setEvents($events);
        /** === Setup Mocks === */
        $mConfig = \Mockery::mock(
            \Praxigento\Composer\Plugin\Templates\Config::class,
            [PRJ_ROOT . '/' . self::FILE_CONFIG_JSON_NVC]
        );
        $mConfig->shouldReceive('getTemplatesForEvent')->with($eventName)->andReturn([$template]);
        $mConfig->shouldReceive('getVars')->andReturn([]);
        $mEvent = \Mockery::mock(\Composer\Installer\PackageEvent::class);
        $mEvent->shouldReceive('getName')->andReturn($eventName);
        $this->mIo
            ->shouldReceive('writeError');
        /** === Call and asserts  === */
        $this->obj->setConfig($mConfig);
        $this->obj->setIo($this->mIo);
        $this->obj->onEvent($mEvent);
    }

}
