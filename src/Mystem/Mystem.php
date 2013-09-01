<?php
namespace Mystem;

/**
 * Class Mystem
 * Helper for execute mystem
 */
class Mystem
{
    /* @var string $mystemPath */
    public static $mystemPath = 'bin/';

    /* @var array $errorOutput */
    public static $errorOutput = array("file", "error.log", "a");

    /**
     * @param string $text
     * @return string[]|bool lexical strings array
     */
    public static function stemm($text)
    {
        $handle = proc_open(self::getMystem() . ' -nie utf-8', array(
            0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => self::$errorOutput
        ), $pipes);

        if (!is_resource($handle)) {
            return false;
        }

        fwrite($pipes[0], $text);
        fclose($pipes[0]);
        $raw = array_filter(explode("\n", stream_get_contents($pipes[1])));
        fclose($pipes[1]);
        proc_close($handle);
        return $raw;
    }

    /**
     * Returns mystem executable depends bit depth of operating system
     * @return string
     */
    private static function getMystem()
    {
        return self::$mystemPath . (PHP_INT_SIZE << 3 === 64 ? 'mystem64' : 'mystem');
    }
}