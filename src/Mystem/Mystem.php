<?php
namespace Mystem;

/**
 * Class Mystem
 * Helper for execute mystem
 */
class Mystem
{
    private static $handle;
    protected static $pipes;

    /* @var string $mystemPath path to mystem binary */
    public static $mystemPath = null;

    /**
     * Runs mystem binary and returns raw morphological data for each word
     * Ex. for 'хрюкотали' returns:
     *   array(2) {
     *      ["text"]=> string(18) "хрюкотали"
     *      ["analysis"]=> array(1) {
     *          [0]=> array(3) {
     *              ["lex"] =>string(18) "хрюкотать"
     *              ["gr"]  =>string(42) "V,несов,нп=прош,мн,изъяв"
     *              ["qual"]=>string(7) "bastard"
     *          }
     *     }
     *   }
     * @param string $text
     * @throws \Exception
     * @return array[] lexical strings associative array
     */
    public static function stemm($text)
    {
        self::procOpen();
        do {
            $endMark = 'end' . rand(99999, PHP_INT_MAX);
        } while (mb_strpos($text, $endMark) !== false);
        fwrite(self::$pipes[0], $text . ".$endMark\n");
        $raw = self::readUntil(self::$pipes[1], $endMark);
        $possibleError = stream_get_contents(self::$pipes[2], 1024);
        if (!empty($possibleError)) {
            throw new \Exception("Error: ".$possibleError);
        }
        $lines = explode("\n", $raw);
        foreach ($lines as &$line) {
            $line = json_decode($line, true);
        }
        $lines = array_filter($lines, function ($value) {
            return !empty($value['analysis']);
        });
        return $lines;
    }

    /**
     * @param $pipe
     * @param string $endMark
     * @return string
     */
    private static function readUntil($pipe, $endMark)
    {
        $w = null;
        $read = array($pipe);
        if (stream_select($read, $w, $e, 4, 1000) == 0) {
            return '';
        }
        $raw = '';
        $newOffset = 0;
        $counter = 0;
        do {
            $offset = $newOffset;
            usleep(500);
            $raw .= stream_get_contents($pipe);
            $newOffset = mb_strlen($raw);
        } while (mb_strpos($raw, $endMark, $offset) == false && $counter++<20);
        return $raw;
    }

    private static function procOpen()
    {
        if (self::$handle !== null) {
            return array();
        }

        self::$handle = proc_open(self::getMystem() . ' -incs --format=json', array(
            0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w")
        ), self::$pipes);

        if (!is_resource(self::$handle)) {
            throw new \Exception("Can't proc_open mystem");
        }
        stream_set_blocking(self::$pipes[1], 0);
        stream_set_blocking(self::$pipes[2], 0);

        register_shutdown_function(array('\Mystem\Mystem', 'destruct'));
    }

    public static function destruct()
    {
        if (self::$handle === null) {
            return false;
        }
        if (is_array(self::$pipes)) {
            foreach (self::$pipes as $pipe) {
                fflush($pipe);
                fclose($pipe);
            }
        }
        proc_terminate(self::$handle);
        proc_close(self::$handle);
        self::$handle = null;
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
