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

class Handler_Test extends \PHPUnit_Framework_TestCase
{
    const CLAZZ = 'Praxigento\Composer\Plugin\Templates\Handler';
    /** @var  \Mockery\MockInterface */
    private $mIo;
    /** @var  \Mockery\MockInterface */
    private $mValidator;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mValidator = \Mockery::mock(\Praxigento\Composer\Plugin\Templates\Handler\ConditionValidator::class);
        $this->mIo = \Mockery::mock(\Composer\IO\IOInterface::class);
        /** common setup for mocks */
        $this->mIo->shouldReceive('write');
    }

    public function test_condition_invalid()
    {
        /** === Test Data === */
        $src = FILE_SRC_DUMP;
        $dst = FILE_DST_DUMP;
        $tmpl = new Template();
        $tmpl->setSource($src);
        $tmpl->setDestination($dst);
        $tmpl->setCanRewrite(true);
        $tmpl->setCondition(new Condition());
        /** === Setup Mocks === */
        //($this->conditionValidator->isValid($tmpl->getCondition(), $this->vars))
        $this->mValidator
            ->shouldReceive('isValid')->once()
            ->andReturn(false);
        $expected = self::CLAZZ . ": <comment>Skip processing of the template ($src) because condition (\${}) is 'false'.</comment>";
        $this->mIo
            ->shouldReceive('write')->once()
            ->with($expected);
        /** === Call and asserts  === */
        $proc = new Handler([], $this->mIo, null, $this->mValidator);
        $proc->process($tmpl);
    }

    public function test_condition_valid()
    {
        /** === Test Data === */
        $src = FILE_SRC_DUMP;
        $dst = FILE_DST_DUMP;
        $tmpl = new Template();
        $tmpl->setSource($src);
        $tmpl->setDestination($dst);
        $tmpl->setCanRewrite(true);
        $tmpl->setCondition(new Condition());
        /** === Setup Mocks === */
        //($this->conditionValidator->isValid($tmpl->getCondition(), $this->vars))
        $this->mValidator
            ->shouldReceive('isValid')->once()
            ->andReturn(true);
        //$this->fileSaver->save($tmpl->getDestination(), $content);
        $mFileSaver = \Mockery::mock(\Praxigento\Composer\Plugin\Templates\Handler\FileSaver::class);
        $mFileSaver
            ->shouldReceive('save')->once();
        $expected = self::CLAZZ . ": <info>Destination file '$dst' is created from source template '$src'.</info>";
        $this->mIo
            ->shouldReceive('write')->once()
            ->with($expected);
        /** === Call and asserts  === */
        $proc = new Handler([], $this->mIo, $mFileSaver, $this->mValidator);
        $proc->process($tmpl);
    }

    public function test_create()
    {
        /** === Test Data === */
        $src = FILE_SRC_DUMP;
        $dst = 'destination_file';
        $vars = ['var1' => '21'];
        $tmpl = new Template();
        $tmpl->setSource($src);
        $tmpl->setDestination($dst);
        /** === Setup Mocks === */
        //$this->fileSaver->save($tmpl->getDestination(), $content);
        $mFileSaver = \Mockery::mock(\Praxigento\Composer\Plugin\Templates\Handler\FileSaver::class);
        $mFileSaver
            ->shouldReceive('save')->once();
        //$this->io->write(__CLASS__ . ": <info>Destination file '{$tmpl->getDestination()}' is created from source template '{$tmpl->getSource()}'.</info>");
        $expected = self::CLAZZ . ": <info>Destination file '$dst' is created from source template '$src'.</info>";
        $this->mIo
            ->shouldReceive('write')->once()
            ->with($expected);
        /** === Call and asserts  === */
        $proc = new Handler($vars, $this->mIo, $mFileSaver);
        $proc->process($tmpl);
    }

    public function test_dstExists_withRewrite()
    {
        /** === Test Data === */
        $src = FILE_SRC_DUMP;
        $dst = FILE_DST_DUMP;
        $saver = new FileSaver();
        $saver->save($dst, 'content');
        $tmpl = new Template();
        $tmpl->setSource($src);
        $tmpl->setDestination($dst);
        $tmpl->setCanRewrite(true);
        /** === Setup Mocks === */
        $expected = self::CLAZZ . ": <info>Destination file '$dst' is created from source template '$src'.</info>";
        $this->mIo
            ->shouldReceive('write')->once()
            ->with($expected);
        //$this->fileSaver->save($tmpl->getDestination(), $content);
        $mFileSaver = \Mockery::mock(\Praxigento\Composer\Plugin\Templates\Handler\FileSaver::class);
        $mFileSaver
            ->shouldReceive('save')->once();
        /** === Call and asserts  === */
        $proc = new Handler([], $this->mIo, $mFileSaver);
        $proc->process($tmpl);
        unlink(FILE_DST_DUMP);
    }

    public function test_dstExists_withoutRewrite()
    {
        /** === Test Data === */
        $src = FILE_SRC_DUMP;
        $dst = FILE_DST_DUMP;
        $saver = new FileSaver();
        $saver->save($dst, 'content');
        $tmpl = new Template();
        $tmpl->setSource($src);
        $tmpl->setDestination($dst);
        $tmpl->setCanRewrite(false);
        /** === Setup Mocks === */
        $expected = self::CLAZZ . ": <comment>Destination file '$dst' is already exist and cannot be rewrote (rewrite = false).</comment>";
        $this->mIo
            ->shouldReceive('write')->once()
            ->with($expected);
        /** === Call and asserts  === */
        $proc = new Handler([], $this->mIo);
        $proc->process($tmpl);
        unlink($dst);
    }

    public function test_srcNotExists()
    {
        /** === Test Data === */
        $src = 'file_is_not_exists';
        $vars = ['var1' => '21'];
        $tmpl = new Template();
        $tmpl->setSource($src);
        /** === Setup Mocks === */
        $expected = self::CLAZZ . ": <error>Cannot open source template ($src).</error>";
        $this->mIo
            ->shouldReceive('writeError')->once()
            ->with($expected);
        /** === Call and asserts  === */
        $proc = new Handler($vars, $this->mIo);
        $proc->process($tmpl);
    }
}