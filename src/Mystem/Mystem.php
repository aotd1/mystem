<?php
namespace Mystem;

/**
 * Class Mystem
 * Helper for execute mystem
 */
class Mystem
{
    /* @var string $mystemPath */
    public static $mystemPath = null;

    /* @var array $errorOutput */
    public static $errorOutput = array("file", "error.log", "a");

    /**
     * @param string $text
     * @throws \Exception
     * @return string[]|bool lexical strings array
     */
    public static function stemm($text)
    {
        $handle = proc_open(self::getMystem() . ' -nie utf-8', array(
            0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w")
        ), $pipes);

        if (!is_resource($handle)) {
            return false;
        }

        fwrite($pipes[0], $text);
        fclose($pipes[0]);
        $raw = array_filter(explode("\n", stream_get_contents($pipes[1])));
        fclose($pipes[1]);
        if ( !feof( $pipes[2] ) ) {
            $logLine = fgets( $pipes[2] );
            if (!empty($logLine)) {
                throw new \Exception($logLine);
            }
        }
        proc_close($handle);
        return $raw;
    }

    /**
     * Returns mystem executable depends bit depth of operating system
     * @return string
     */
    private static function getMystem()
    {
        if (self::$mystemPath === null) {
            self::$mystemPath = __DIR__ . '/../../bin/';
        }
        return self::$mystemPath . (PHP_INT_SIZE << 3 === 64 ? 'mystem64' : 'mystem');
    }
}