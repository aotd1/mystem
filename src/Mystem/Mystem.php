<?php
namespace Mystem;

/**
 * Class Mystem
 * Helper for execute mystem
 */
class Mystem
{
    /* @var string $mystemPath path to mystem binary */
    public static $mystemPath = null;

    /**
     * Runs mystem binary and returns raw morphological data for each word
     * Ex. for 'каракули' returns:
     *   каракули{каракуль=S,муж,неод=им,мн|=S,муж,неод=вин,мн|каракуля=S,жен,неод=им,мн|=S,жен,неод=род,ед}
     * @param string $text
     * @throws \Exception
     * @return string[] lexical strings array
     */
    public static function stemm($text)
    {
        $handle = proc_open(self::getMystem() . ' -ni', array(
            0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w")
        ), $pipes);

        if (!is_resource($handle)) {
            throw new \Exception("Can't proc_open mystem");
        }

        fwrite($pipes[0], $text);
        fclose($pipes[0]);
        $raw = array_filter(explode("\n", stream_get_contents($pipes[1])));
        fclose($pipes[1]);
        if (!feof($pipes[2])) {
            $logLine = fgets($pipes[2]);
            if (!empty($logLine)) {
                throw new \Exception($logLine);
            }
        }
        proc_close($handle);
        return $raw;
    }

    /**
     * Returns mystem executable depends bit depth of operating system and OS type
     * @return string
     */
    private static function getMystem()
    {
        if (self::$mystemPath === null) {
            if (is_dir(__DIR__ . '/../../vendor/bin/')) {
                self::$mystemPath = __DIR__ . '/../../vendor/bin/';
            } else {
                self::$mystemPath = __DIR__ . '/../../../../bin/';
            }
        }

        return self::$mystemPath . (
        strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'mystem.exe' : 'mystem'
        );
    }
}
