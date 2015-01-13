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
 * Configuration parser.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Composer\Plugin\Templates;


class Config {
	const CFG_DST = 'dst';
	const CFG_EVENTS = 'events';
	const CFG_REWRITE = 'rewrite';
	const CFG_SRC = 'src';
	const CFG_TMPL = 'templates';
	const CFG_VARS = 'vars';
	/**
	 * Array of the all available events from \Composer\Script\ScriptEvents
	 * @var array
	 */
	private static $EVENTS_ALL;
	/**
	 * Raw data from plugin configuration file.
	 * @var mixed
	 */
	private $rawData;
	/**
	 * Templates itself related configuration.
	 * @var Config\Template[]
	 */
	private $templates = array();
	/**
	 * Template variables related configuration.
	 * @var array
	 */
	private $vars = array();

	function __construct($configFilename) {
		$string        = file_get_contents($configFilename);
		$this->rawData = json_decode($string, true);
		/* vars */
		if(isset($this->rawData[ self::CFG_VARS ])) {
			$this->vars = $this->rawData[ self::CFG_VARS ];
		}
		/* templates */
		if(isset($this->rawData[ self::CFG_TMPL ])) {
			$tmpls = $this->rawData[ self::CFG_TMPL ];
			foreach($tmpls as $one) {
				$this->parseTemplates($one);
			}
		}
	}

	/**
	 * * @return array of all available events (see \Composer\Script\ScriptEvents)
	 */
	public static function getEventsAvailable() {
		if(is_null(self::$EVENTS_ALL)) {
			$refl             = new \ReflectionClass('\Composer\Script\ScriptEvents');
			self::$EVENTS_ALL = $refl->getConstants();
		}
		return self::$EVENTS_ALL;
	}

	/**
	 * @return array of all events registered to be processed.
	 */
	public function getEventsEnabled() {
		$result = array();
		foreach($this->templates as $tmpl) {
			foreach($tmpl->getEvents() as $one) {
				$result[ $one ] = $one;
			}
		}
		return $result;
	}

	/**
	 * @return array
	 */
	public function getTemplates() {
		return $this->templates;
	}

	/**
	 * Return array of the templates to be processed on the event $name.
	 *
	 * @param $name
	 *
	 * @return Config\Template[]
	 */
	public function getTemplatesForEvent($name) {
		$result = array();
		foreach($this->templates as $tmpl) {
			foreach($tmpl->getEvents() as $event) {
				if($event == $name) {
					$result[] = $tmpl;
					break;
				}
			}
		}
		return $result;
	}

	/**
	 * @return array
	 */
	public function getVars() {
		return $this->vars;
	}

	/**
	 * Merge other config object to the current.
	 *
	 * @param Config $other
	 */
	public function merge(Config $other) {
		/* merge variables */
		foreach($other->getVars() as $key => $value) {
			$this->vars[ $key ] = $value;
		}
		/* merge templates */
		foreach($other->getTemplates() as $one) {
			$this->templates[] = $one;
		}
	}

	private function isEvent($name) {
		$result = false;
		foreach(self::getEventsAvailable() as $one) {
			if($one == $name) {
				$result = true;
				break;
			}
		}
		return $result;
	}

	private function parseTemplateEvents($unparsed) {
		$result = array();
		if(is_string($unparsed)) {
			$name = strtolower(trim($unparsed));
			if($this->isEvent($name)) {
				$result[] = $name;
			}
		} else if(is_array($unparsed)) {
			foreach($unparsed as $one) {
				$name = strtolower(trim($one));
				if($this->isEvent($name)) {
					$result[] = $name;
				}
			}
		}
		return $result;
	}

	private function parseTemplates($unparsed) {
		if(
			isset($unparsed[ self::CFG_SRC ]) &&
			isset($unparsed[ self::CFG_DST ]) &&
			isset($unparsed[ self::CFG_EVENTS ])
		) {
			$tmpl = new Config\Template();
			$tmpl->setSource($unparsed[ self::CFG_SRC ]);
			$tmpl->setDestination($unparsed[ self::CFG_DST ]);
			if(isset($unparsed[ self::CFG_REWRITE ])) {
				$tmpl->setCanRewrite($unparsed[ self::CFG_REWRITE ]);
			}
			/* exclude missed events */
			if(
				(
					is_string($unparsed[ self::CFG_EVENTS ]) &&
					(strlen($unparsed[ self::CFG_EVENTS ]) == 0)
				) || (
					is_array($unparsed[ self::CFG_EVENTS ]) &&
					(count($unparsed[ self::CFG_EVENTS ]) == 0)
				)
			) {
				/* skip entry*/
			} else {
				$events = $this->parseTemplateEvents($unparsed[ self::CFG_EVENTS ]);
				$tmpl->setEvents($events);
				$this->templates[] = $tmpl;
			}
		} else {
			/* just skip rows without src, dst and events */
		}
	}
}