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

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\ScriptEvents;

class Main_Test extends \PHPUnit_Framework_TestCase {

	public function test_act() {
		$plugin = new Main();
		/** @var  $package Composer\Package\RootPackageInterface */
		$package = $this->getMockBuilder('Composer\Package\RootPackageInterface')->getMock();
		$package->method('getExtra')->willReturn(array( 'praxigento_templates_config' => './instance_cfg.json' ));
		/** @var  $stub Composer */
		$composer = $this->getMockBuilder('Composer\Composer')->getMock();
		$composer->method('getPackage')->willReturn($package);
		/** @var  $io IOInterface */
		$io = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
		$plugin->activate($composer, $io);
	}

	public function test_getSubscribedEvents() {
		$events = Main::getSubscribedEvents();
		$this->assertArrayHasKey(ScriptEvents::POST_INSTALL_CMD, $events);
		$this->assertArrayHasKey(ScriptEvents::POST_UPDATE_CMD, $events);
	}

	public function test_onPostInstallCmd() {
		$plugin = new Main();
		/** @var  $event Composer\Script\CommandEvent */
		$event = $this->getMockBuilder('\Composer\Script\CommandEvent')->disableOriginalConstructor()->getMock();
		$plugin->onPostInstallCmd($event);
	}

	public function test_onPostUpdateCmd() {
		$plugin = new Main();
		/** @var  $event Composer\Script\CommandEvent */
		$event = $this->getMockBuilder('\Composer\Script\CommandEvent')->disableOriginalConstructor()->getMock();
		$plugin->onPostUpdateCmd($event);
	}
}
