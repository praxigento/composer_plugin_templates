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
	const FILE_CONFIG_JSON_NVC = 'test/data/templates_not_under_vc.json';
	const FILE_CONFIG_JSON_VC = 'test/data/templates_under_vc.json';
	const FILE_TMPL_DST = 'test/bin/dump_db/dump.sh';
	const FILE_TMPL_SRC = 'test/tmpl/dump.sh';
	/** @var  string Root directory for the plugin to use test related files and folders. */
	private static $ROOT_DIR;

	/**
	 * Traverse up to 'phpunit.xml.dist' and save root folder.
	 */
	public static function setUpBeforeClass() {
		$dir  = './';
		$file = 'phpunit.xml.dist';
		for($i = 0; $i < 32; $i++) {
			if(file_exists($dir . $file)) {
				break;
			} else {
				$dir .= '../';
			}
		}
		self::$ROOT_DIR = $dir;
	}

	public function test_activate_withExtra() {
		$FILENAME = self::$ROOT_DIR . self::FILE_CONFIG_JSON_NVC;
		/** @var  $package Composer\Package\RootPackageInterface */
		$package = $this->getMockBuilder('Composer\Package\RootPackageInterface')->getMock();
		$package->method('getExtra')->willReturn(array( Main::EXTRA_PARAM => $FILENAME ));
		/** @var  $stub Composer */
		$composer = $this->getMockBuilder('Composer\Composer')->getMock();
		$composer->method('getPackage')->willReturn($package);
		/** @var  $io IOInterface */
		$io     = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
		$plugin = new Main();
		$plugin->activate($composer, $io);
		$this->assertEquals($FILENAME, $plugin->getConfigFileNames()[0]);
	}

	public function test_activate_withExtra_asArray() {
		$FILENAME_1 = self::$ROOT_DIR . self::FILE_CONFIG_JSON_NVC;
		$FILENAME_2 = self::$ROOT_DIR . self::FILE_CONFIG_JSON_VC;
		/** @var  $package Composer\Package\RootPackageInterface */
		$package = $this->getMockBuilder('Composer\Package\RootPackageInterface')->getMock();
		$package->method('getExtra')->willReturn(array( Main::EXTRA_PARAM => array( $FILENAME_1, $FILENAME_2 ) ));
		/** @var  $stub Composer */
		$composer = $this->getMockBuilder('Composer\Composer')->getMock();
		$composer->method('getPackage')->willReturn($package);
		/** @var  $io IOInterface */
		$io     = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
		$plugin = new Main();
		$plugin->activate($composer, $io);
		$this->assertEquals($FILENAME_1, $plugin->getConfigFileNames()[0]);
		$this->assertEquals($FILENAME_2, $plugin->getConfigFileNames()[1]);
	}

	public function test_activate_withExtra_wrongFile() {
		$FILENAME = self::$ROOT_DIR . self::FILE_CONFIG_JSON_NVC;
		/** @var  $package Composer\Package\RootPackageInterface */
		$package = $this->getMockBuilder('Composer\Package\RootPackageInterface')->getMock();
		$package->method('getExtra')->willReturn(array( Main::EXTRA_PARAM => $FILENAME . '_missedFile' ));
		/** @var  $stub Composer */
		$composer = $this->getMockBuilder('Composer\Composer')->getMock();
		$composer->method('getPackage')->willReturn($package);
		/** @var  $io IOInterface */
		$io = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
		$io->expects($this->once())->method('write');
		$plugin = new Main();
		$plugin->activate($composer, $io);
	}

	public function test_activate_withoutExtra() {
		$plugin = new Main();
		/** @var  $package Composer\Package\RootPackageInterface */
		$package = $this->getMockBuilder('Composer\Package\RootPackageInterface')->getMock();
		$package->method('getExtra')->willReturn(array());
		/** @var  $stub Composer */
		$composer = $this->getMockBuilder('Composer\Composer')->getMock();
		$composer->method('getPackage')->willReturn($package);
		/** @var  $io IOInterface */
		$io = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
		$plugin->activate($composer, $io);
		$this->assertNull($plugin->getConfigFileNames());
	}

	public function test_getSubscribedEvents() {
		$events = Main::getSubscribedEvents();
		$refl   = new \ReflectionClass('\Composer\Script\ScriptEvents');
		foreach($refl->getConstants() as $one) {
			$this->assertArrayHasKey(ScriptEvents::POST_INSTALL_CMD, $events);
		}
	}


	public function test_onEvent() {
		$eventName = ScriptEvents::POST_INSTALL_CMD;
		$template  = new Config\Template();
		$template->setSource(self::FILE_TMPL_SRC);
		$template->setDestination(self::FILE_TMPL_DST);
		$events = array( $eventName );
		$template->setEvents($events);
		$config = $this->getMockBuilder('\Praxigento\Composer\Plugin\Templates\Config')->disableOriginalConstructor()->getMock();
		$config->method('getTemplatesForEvent')->with($eventName)->willReturn(array( $template ));
		$config = new Config(self::$ROOT_DIR . self::FILE_CONFIG_JSON_NVC);
		/** @var  $io IOInterface */
		$io = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
		/** @var  $event Composer\Script\CommandEvent */
		$event = $this->getMockBuilder('\Composer\Script\CommandEvent')->disableOriginalConstructor()->getMock();
		$event->method('getName')->willReturn($eventName);
		/** @var  $plugin Main */
		$plugin = new Main();
		$plugin->setConfig($config);
		$plugin->setIo($io);
		$plugin->onEvent($event);
	}

}
