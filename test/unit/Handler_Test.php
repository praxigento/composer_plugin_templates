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

use Praxigento\Composer\Plugin\Templates\Config\Condition;
use Praxigento\Composer\Plugin\Templates\Config\Template;
use Praxigento\Composer\Plugin\Templates\Handler\FileSaver;

require_once(__DIR__ . '/../../phpunit.bootstrap.php');

class Handler_Test extends \PHPUnit_Framework_TestCase {
    const CLAZZ = 'Praxigento\Composer\Plugin\Templates\Handler';

    public function test_create() {
        $src = FILE_SRC_DUMP;
        $dst = 'destination_file';
        /* Prepare test mocks and data */
        $vars = [ 'var1' => '21' ];
        $tmpl = new Template();
        $tmpl->setSource($src);
        $tmpl->setDestination($dst);
        //$this->fileSaver->save($tmpl->getDestination(), $content);
        $mockFileSaver = $this
            ->getMockBuilder('Praxigento\Composer\Plugin\Templates\Handler\FileSaver')
            ->getMock();
        $mockFileSaver
            ->expects($this->once())
            ->method('save');
        //$this->io->write(__CLASS__ . ": <info>Destination file '{$tmpl->getDestination()}' is created from source template '{$tmpl->getSource()}'.</info>");
        /* $mockIo */
        $mockIo = $this
            ->getMockBuilder('Composer\IO\IOInterface')
            ->getMock();
        $expected = self::CLAZZ . ": <info>Destination file '$dst' is created from source template '$src'.</info>";
        $mockIo
            ->expects($this->once())
            ->method('write')
            ->with($expected);
        /* Create object and perform testing action. */
        $proc = new Handler($vars, $mockIo, $mockFileSaver);
        $proc->process($tmpl);
    }

    public function test_srcNotExists() {
        /* Prepare test mocks and data */
        $src = 'file_is_not_exists';
        $mockIo = $this
            ->getMockBuilder('Composer\IO\IOInterface')
            ->getMock();
        $expected = self::CLAZZ . ": <error>Cannot open source template ($src).</error>";
        $mockIo
            ->expects($this->once())
            ->method('writeError')
            ->with($expected);
        $vars = [ 'var1' => '21' ];
        $tmpl = new Template();
        $tmpl->setSource($src);
        /* Create object and perform testing action. */
        $proc = new Handler($vars, $mockIo);
        $proc->process($tmpl);
    }

    public function test_dstExists_withRewrite() {
        /* Prepare test mocks and data */
        $src = FILE_SRC_DUMP;
        $dst = FILE_DST_DUMP;
        $saver = new FileSaver();
        $saver->save($dst, 'content');
        $mockIo = $this
            ->getMockBuilder('Composer\IO\IOInterface')
            ->getMock();
        $expected = self::CLAZZ . ": <info>Destination file '$dst' is created from source template '$src'.</info>";
        $mockIo
            ->expects($this->once())
            ->method('write')
            ->with($expected);
        //$this->fileSaver->save($tmpl->getDestination(), $content);
        $mockFileSaver = $this
            ->getMockBuilder('Praxigento\Composer\Plugin\Templates\Handler\FileSaver')
            ->getMock();
        $mockFileSaver
            ->expects($this->once())
            ->method('save');
        $tmpl = new Template();
        $tmpl->setSource($src);
        $tmpl->setDestination($dst);
        $tmpl->setCanRewrite(true);
        /* Create object and perform testing action. */
        $proc = new Handler([ ], $mockIo, $mockFileSaver);
        $proc->process($tmpl);
        unlink(FILE_DST_DUMP);
    }

    public function test_dstExists_withoutRewrite() {
        /* Prepare test mocks and data */
        $src = FILE_SRC_DUMP;
        $dst = FILE_DST_DUMP;
        $saver = new FileSaver();
        $saver->save($dst, 'content');
        $mockIo = $this
            ->getMockBuilder('Composer\IO\IOInterface')
            ->getMock();
        $expected = self::CLAZZ . ": <comment>Destination file '$dst' is already exist and cannot be rewrote (rewrite = false).</comment>";
        $mockIo
            ->expects($this->once())
            ->method('write')
            ->with($expected);
        $tmpl = new Template();
        $tmpl->setSource($src);
        $tmpl->setDestination($dst);
        $tmpl->setCanRewrite(false);
        /* Create object and perform testing action. */
        $proc = new Handler([ ], $mockIo);
        $proc->process($tmpl);
        unlink($dst);
    }

    public function test_condition_valid() {
        /* Prepare test mocks and data */
        $src = FILE_SRC_DUMP;
        $dst = FILE_DST_DUMP;
        //($this->conditionValidator->isValid($tmpl->getCondition(), $this->vars))
        $mockValidator = $this
            ->getMockBuilder('Praxigento\Composer\Plugin\Templates\Handler\ConditionValidator')
            ->getMock();
        $mockValidator
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);
        //$this->fileSaver->save($tmpl->getDestination(), $content);
        $mockFileSaver = $this
            ->getMockBuilder('Praxigento\Composer\Plugin\Templates\Handler\FileSaver')
            ->getMock();
        $mockFileSaver
            ->expects($this->once())
            ->method('save');

        // IO
        $mockIo = $this
            ->getMockBuilder('Composer\IO\IOInterface')
            ->getMock();
        $expected = self::CLAZZ . ": <info>Destination file '$dst' is created from source template '$src'.</info>";
        $mockIo
            ->expects($this->once())
            ->method('write')
            ->with($expected);
        // template
        $tmpl = new Template();
        $tmpl->setSource($src);
        $tmpl->setDestination($dst);
        $tmpl->setCanRewrite(true);
        $tmpl->setCondition(new Condition());
        /* Create object and perform testing action. */
        $proc = new Handler([ ], $mockIo, $mockFileSaver, $mockValidator);
        $proc->process($tmpl);
    }

    public function test_condition_invalid() {
        /* Prepare test mocks and data */
        $src = FILE_SRC_DUMP;
        $dst = FILE_DST_DUMP;
        //($this->conditionValidator->isValid($tmpl->getCondition(), $this->vars))
        $mockValidator = $this
            ->getMockBuilder('Praxigento\Composer\Plugin\Templates\Handler\ConditionValidator')
            ->getMock();
        $mockValidator
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false);
        // IO
        $mockIo = $this
            ->getMockBuilder('Composer\IO\IOInterface')
            ->getMock();
        $expected = self::CLAZZ . ": <comment>Skip processing of the template ($src) because condition (\${}) is 'false'.</comment>";
        $mockIo
            ->expects($this->once())
            ->method('write')
            ->with($expected);
        // template
        $tmpl = new Template();
        $tmpl->setSource($src);
        $tmpl->setDestination($dst);
        $tmpl->setCanRewrite(true);
        $tmpl->setCondition(new Condition());
        /* Create object and perform testing action. */
        $proc = new Handler([ ], $mockIo, null, $mockValidator);
        $proc->process($tmpl);
    }
}