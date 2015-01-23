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
use Praxigento\Composer\Plugin\Templates\Config\Template;

class TemplateHandler_Test extends \PHPUnit_Framework_TestCase
{
    /** @var  string Root directory for the plugin to use test related files and folders. */
    private static $ROOT_DIR;

    /**
     * Traverse up to 'phpunit.xml.dist' and save root folder.
     */
    public static function setUpBeforeClass()
    {
        $dir = './';
        $file = 'phpunit.xml.dist';
        for ($i = 0; $i < 32; $i++) {
            if (file_exists($dir . $file)) {
                break;
            } else {
                $dir .= '../';
            }
        }
        self::$ROOT_DIR = $dir;
    }

    public function test_saveFile0()
    {
        /** prepare templates */
        $config = new Config(self::$ROOT_DIR . Main_Test::FILE_CONFIG_JSON_NVC);
        $vars=$config->getVars();

        $tmpls = $config->getTemplates();

        /** First Test */
        $io0 = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
        $tmpl0=reset($tmpls);
        $proc=new TemplateHandler($vars, $io0);
        $io0->expects($this->once())->method('write')->with(get_class($proc) . ": Cannot open source template ({$tmpl0->getSource()}).");
        $proc->process($tmpl0);

        /** Second Test */
        $io1 = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
        $tmpl1=end($tmpls);
        $proc=new TemplateHandler($vars, $io1);
        $io1->expects($this->once())->method('write')->with(get_class($proc) . ": Destination file '{$tmpl1->getDestination()}' is created from source template '{$tmpl1->getSource()}'.");
        $proc->process($tmpl1);

        /** Third Test */
        $io2 = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
        $tmpl2=end($tmpls);
        $tmpl2->setCanRewrite(false);
        $proc=new TemplateHandler($vars, $io2);
        $io2->expects($this->once())->method('write')->with(get_class($proc) . ": Destination file '{$tmpl2->getDestination()}' is already exist and cannot be rewrote (rewrite = false).");
        $proc->process($tmpl2);

        /** Fourth Test (directory creating) */

        $fullpath='testino/bin/dump_db_new/dump.sh';

        $io3 = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();
        $tmpl3=end($tmpls);
        $tmpl3->setDestination($fullpath);
        $proc=new TemplateHandler($vars, $io3);
        $proc->process($tmpl3);

        $parts = explode(TemplateHandler::DS, $fullpath);
        /* remove filename from array */
        array_pop($parts);
        $dir = array_shift($parts);
        $this->assertTrue(is_dir($dir));
        unlink($fullpath);
        rmdir($dir . '/bin/dump_db_new');
        rmdir($dir . '/bin');
        rmdir($dir);

    }

}