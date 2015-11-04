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
namespace Praxigento\Composer\Plugin\Templates\Handler;

require_once(__DIR__ . '/../../../phpunit.bootstrap.php');

class FileSaver_Test extends \PHPUnit_Framework_TestCase {

    public function test_file_only() {
        /* Prepare test mocks and data */
        $path = 'some_file_to_save.ext';
        if(file_exists($path)) {
            unlink($path);
        }
        /* Create object and perform testing action. */
        $obj = new FileSaver();
        $obj->save($path, 'content');
        $this->assertTrue(file_exists($path));
        unlink($path);
    }

    public function test_file_from_current_dir() {
        /* Prepare test mocks and data */
        $path = './some_file_to_save.ext';
        if(file_exists($path)) {
            unlink($path);
        }
        /* Create object and perform testing action. */
        $obj = new FileSaver();
        $obj->save($path, 'content');
        $this->assertTrue(file_exists($path));
        unlink($path);
    }

    public function test_relaltive_path() {
        /* Prepare test mocks and data */
        $dir = './dir';
        $path = $dir . '/some_file_to_save.ext';
        if(file_exists($path)) {
            unlink($path);
        }
        /* Create object and perform testing action. */
        $obj = new FileSaver();
        $obj->save($path, 'content');
        $this->assertTrue(file_exists($path));
        unlink($path);
        rmdir($dir);
    }

    public function test_relaltive_path_without_point() {
        /* Prepare test mocks and data */
        $dir = 'dir';
        $path = $dir . '/some_file_to_save.ext';
        if(file_exists($path)) {
            unlink($path);
        }
        /* Create object and perform testing action. */
        $obj = new FileSaver();
        $obj->save($path, 'content');
        $this->assertTrue(file_exists($path));
        unlink($path);
        rmdir($dir);
    }

    public function test_absolute_path() {
        /* Prepare test mocks and data */
        $path = __DIR__ . '/some_file_to_save.ext';
        if(file_exists($path)) {
            unlink($path);
        }
        /* Create object and perform testing action. */
        $obj = new FileSaver();
        $obj->save($path, 'content');
        $this->assertTrue(file_exists($path));
        unlink($path);
    }
}