<?php namespace AAD\Cache\File;

class Helper
{
    public static function remove($dir)
    {
        $scan = scandir($dir);
        
        foreach ($scan as $key => $value) {
            if (!in_array($value, [".", ".."])) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    self::remove($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    @unlink($dir . DIRECTORY_SEPARATOR . $value);
                }
            }
        }

        @rmdir($dir);
    }

    /**
     * @todo use the recursive parameter
     */
    public function mkdir($dir)
    {
        if (!is_dir($dir)) {
            @mkdir($dir);
        }
    }

    public function put(string $path, $content)
    {
        @touch($path);
        return @file_put_contents($path, $content) !== false;
    }
}
