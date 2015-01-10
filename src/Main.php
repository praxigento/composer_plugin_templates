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
 * Main class for the plugin.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Composer\Plugin\Templates;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\CommandEvent;
use Composer\Script\ScriptEvents;
use Praxigento\Composer\Plugin\Templates\Config;

class Main implements PluginInterface, EventSubscriberInterface {
	/** Entry name for plugin config file in 'extra' section of composer.json */
	const EXTRA_PARAM = 'praxigento_composer_plugin_mage_config';
	/** @var Composer */
	protected $composer;
	/** @var Config */
	protected $config;
	/** @var IOInterface */
	protected $io;
	/** @var  string Name of the plugin's configuration file. */
	private $configFileName;

	/**
	 * Returns an array of event names this subscriber wants to listen to.
	 *
	 * The array keys are event names and the value can be:
	 *
	 * * The method name to call (priority defaults to 0)
	 * * An array composed of the method name to call and the priority
	 * * An array of arrays composed of the method names to call and respective
	 *   priorities, or 0 if unset
	 *
	 * For instance:
	 *
	 * * array('eventName' => 'methodName')
	 * * array('eventName' => array('methodName', $priority))
	 * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
	 *
	 * @return array The event names to listen to
	 */
	public static function getSubscribedEvents() {
		$result = array(
			ScriptEvents::POST_INSTALL_CMD => array(
				array( 'onPostInstallCmd', 0 )
			),
			ScriptEvents::POST_UPDATE_CMD  => array(
				array( 'onPostUpdateCmd', 0 )
			),
		);
		return $result;
	}

	/**
	 * Apply plugin modifications to composer
	 *
	 * @param Composer    $composer
	 * @param IOInterface $io
	 */
	public function activate(Composer $composer, IOInterface $io) {
		$this->composer = $composer;
		$this->io       = $io;
		$extra          = $composer->getPackage()->getExtra();
		if(isset($extra[ self::EXTRA_PARAM ])) {
			$this->configFileName = $extra[ self::EXTRA_PARAM ];
			/* parse configuration */
			if(file_exists($this->configFileName)) {
				$this->config = new Config($this->configFileName);
			} else {
				$io->write(__CLASS__ . ": Cannot open configuration file (" . $this->configFileName . ").", true);
			}

		}
	}

	/**
	 * @return mixed
	 */
	public
	function getConfigFileName() {
		return $this->configFileName;
	}

	public
	function onPostInstallCmd(
		CommandEvent $event
	) {

	}

	public
	function onPostUpdateCmd(
		CommandEvent $event
	) {

	}
}