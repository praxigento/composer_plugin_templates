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
	 * Raw data from plugin configuration file.
	 * @var mixed
	 */
	private $rawData;
	/**
	 * Templates itself related configuration.
	 * @var array
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
				$this->parseTemplateEntry($one);
			}
		}
	}

	/**
	 * @return array
	 */
	public function getTemplates() {
		return $this->templates;
	}

	/**
	 * @return array
	 */
	public function getVars() {
		return $this->vars;
	}

	private function parseTemplateEntry($unparsed) {
		if(
			isset($unparsed[ self::CFG_SRC ]) &&
			isset($unparsed[ self::CFG_DST ]) &&
			isset($unparsed[ self::CFG_EVENTS ])
		) {
			$entry = new Config\Entry();
			$entry->setSource($unparsed[ self::CFG_SRC ]);
			$entry->setDestination($unparsed[ self::CFG_DST ]);
			if(isset($unparsed[ self::CFG_REWRITE ])) {
				$entry->setCanRewrite($unparsed[ self::CFG_REWRITE ]);
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
				$this->templates[] = $entry;
			}
		} else {
			/* just skip rows without src, dst and events */
		}
	}
}