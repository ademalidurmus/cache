<?php namespace AAD\Cache\File;

use PHPUnit\Framework\TestCase;

final class HelperTest extends TestCase
{
    const TEST_DIR = __DIR__ . "/_file_system";

    public function testRemoveFile()
    {
        @mkdir(self::TEST_DIR);
        @touch(self::TEST_DIR . "/file1");
        @touch(self::TEST_DIR . "/file2");
        @mkdir(self::TEST_DIR . "/folder");
        @touch(self::TEST_DIR . "/folder/file1");
        @mkdir(self::TEST_DIR . "/folder/folder");
        @touch(self::TEST_DIR . "/folder/folder/file1");
        @touch(self::TEST_DIR . "/folder/folder/file2");
        @touch(self::TEST_DIR . "/folder/folder/file3");
        Helper::remove(self::TEST_DIR);
        $this->assertFalse(is_dir(self::TEST_DIR));
    }
}
