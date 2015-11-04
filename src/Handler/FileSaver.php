<?php
/**
 * Create full path to file and save content.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Composer\Plugin\Templates\Handler;

class FileSaver {

    /**
     * Thanks for Trent Tompkins: http://php.net/manual/ru/function.file-put-contents.php#84180
     *
     * @param $fullpath
     * @param $contents
     */
    public function save($fullpath, $contents) {
        $parts = explode(DIRECTORY_SEPARATOR, $fullpath);
        /* remove filename from array */
        array_pop($parts);
        $dir = array_shift($parts);
        if($dir && !is_dir($dir)) {
            mkdir($dir);
        }
        foreach($parts as $part) {
            if(!is_dir($dir .= DIRECTORY_SEPARATOR . $part)) {
                mkdir($dir);
            }
        }
        file_put_contents($fullpath, $contents);
    }
}