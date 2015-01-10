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


class Config_Test extends \PHPUnit_Framework_TestCase {
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

	public function test_constructor() {
		$FILE   = self::$ROOT_DIR . Main_Test::FILE_CONFIG_JSON;
		$config = new Config($FILE);
		$this->assertTrue(is_array($config->getVars()));
		$this->assertEquals(4, count($config->getVars()));
		$this->assertTrue(is_array($config->getTemplates()));
		$this->assertEquals(2, count($config->getTemplates()));
	}
}
