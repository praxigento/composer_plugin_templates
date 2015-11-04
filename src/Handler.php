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
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Composer\Plugin\Templates;

use Composer\IO\IOInterface;

class Handler {
    /**
     * See http://symfony.com/doc/current/components/console/introduction.html for more
     * Available tags are: [info|comment|question|error]
     *
     * @var \Composer\IO\IOInterface
     */
    private $io;
    /**
     * Array of the template vars.
     * @var array
     */
    private $vars;
    /**
     * To create full path to the file on save.
     * @var Handler\FileSaver
     */
    private $fileSaver;
    /**
     * To validate conditions.
     * @var null|Handler\ConditionValidator
     */
    private $conditionValidator;

    /**
     * Handler constructor.
     *
     * @param                                 $vars array of the template vars
     * @param IOInterface                     $io IO object from Composer
     * @param Handler\FileSaver|null          $fileSaver
     * @param Handler\ConditionValidator|null $conditionValidator
     */
    function __construct(
        $vars,
        IOInterface $io,
        Handler\FileSaver $fileSaver = null,
        Handler\ConditionValidator $conditionValidator = null
    ) {
        $this->vars = $vars;
        $this->io = $io;
        $this->fileSaver = is_null($fileSaver) ? new Handler\FileSaver() : $fileSaver;
        $this->conditionValidator = is_null($conditionValidator) ? new Handler\ConditionValidator() : $conditionValidator;
    }

    public function process(Config\Template $tmpl) {
        /* process template if condition is not set or condition is valid */
        if(
            ($tmpl->getCondition() == null) ||
            ($this->conditionValidator->isValid($tmpl->getCondition(), $this->vars))
        ) {
            /* load source file */
            if(is_file($tmpl->getSource())) {
                $content = file_get_contents($tmpl->getSource());
                /* replace all vars by values */
                if(is_array($this->vars)) {
                    foreach($this->vars as $key => $value) {
                        $content = str_replace('${' . $key . '}', $value, $content);
                    }
                }
                /* save destination file */
                if(is_file($tmpl->getDestination()) && !$tmpl->isCanRewrite()) {
                    $this->io->write(__CLASS__ . ": <comment>Destination file '{$tmpl->getDestination()}' is already exist and cannot be rewrote (rewrite = false).</comment>");
                } else {
                    $this->fileSaver->save($tmpl->getDestination(), $content);
                    $this->io->write(__CLASS__ . ": <info>Destination file '{$tmpl->getDestination()}' is created from source template '{$tmpl->getSource()}'.</info>");
                }
            } else {
                $this->io->writeError(__CLASS__ . ": <error>Cannot open source template ({$tmpl->getSource()}).</error>");
            }
        } else {
            /* there is wrong condition for template */
            $outSrc = $tmpl->getSource();
            $cond = $tmpl->getCondition();
            $outCond = '${' . $cond->getVar() . '}' . $cond->getOperation() . $cond->getValue();
            $this->io->write(__CLASS__ . ": <comment>Skip processing of the template ($outSrc) because condition ($outCond) is 'false'.</comment>");
        }
    }


}