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

class Config
{
    const CFG_CONDITION = 'condition';
    const CFG_COND_OPER = 'operation';
    const CFG_COND_VALUE = 'value';
    const CFG_COND_VAR = 'var';
    const CFG_DST = 'dst';
    const CFG_REWRITE = 'rewrite';
    const CFG_SRC = 'src';
    const CFG_TMPL = 'templates';
    const CFG_VARS = 'vars';

    /**
     * Templates itself related configuration.
     * @var Config\Template[]
     */
    private $templates = [];
    /**
     * Template variables related configuration.
     * @var array
     */
    private $vars = [];

    function __construct($configFilename)
    {
        $string = file_get_contents($configFilename);
        $rawData = json_decode($string, true);
        /* vars */
        if (isset($rawData[self::CFG_VARS])) {
            $this->vars = $rawData[self::CFG_VARS];
        }
        /* templates */
        if (isset($rawData[self::CFG_TMPL])) {
            $tmpls = $rawData[self::CFG_TMPL];
            foreach ($tmpls as $one) {
                $this->parseTemplates($one);
            }
        }
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * Return 'true' if configuration has parsed data.
     * @return bool
     */
    public function hasData()
    {
        $result =
            (count($this->vars) > 0) ||
            (count($this->templates) > 0);
        return $result;
    }

    /**
     * Merge other config object to the current.
     *
     * @param Config $other
     */
    public function merge(Config $other)
    {
        /* merge variables */
        foreach ($other->getVars() as $key => $value) {
            $this->vars[$key] = $value;
        }
        /* merge templates */
        foreach ($other->getTemplates() as $one) {
            $this->templates[] = $one;
        }
    }

    private function parseTemplateCondition($rowData)
    {
        $result = new \Praxigento\Composer\Plugin\Templates\Config\Condition();
        if (is_array($rowData)) {
            foreach ($rowData as $key => $one) {
                $name = strtolower(trim($key));
                switch ($name) {
                    case self::CFG_COND_OPER:
                        $result->setOperation($one);
                        break;
                    case self::CFG_COND_VALUE:
                        $result->setValue($one);
                        break;
                    case self::CFG_COND_VAR:
                        $result->setVar($one);
                        break;
                }
            }
        }
        return $result;
    }

    private function parseTemplates($input)
    {
        if (
            isset($input[self::CFG_SRC]) &&
            isset($input[self::CFG_DST])
        ) {
            $tmpl = new Config\Template();
            $tmpl->setSource($input[self::CFG_SRC]);
            $tmpl->setDestination($input[self::CFG_DST]);
            /* parse 'rewrite' item */
            if (isset($input[self::CFG_REWRITE])) {
                $tmpl->setCanRewrite($input[self::CFG_REWRITE]);
            }
            /* parse 'condition' item */
            if (isset($input[self::CFG_CONDITION])) {
                $condition = $this->parseTemplateCondition($input[self::CFG_CONDITION]);
                $tmpl->setCondition($condition);
            }
            $this->templates[] = $tmpl;
        } else {
            /* just skip rows without src & dst */
        }
    }
}