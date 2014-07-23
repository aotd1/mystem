<?php

use \Mystem\Mystem;

class MystemTest extends \PHPUnit_Framework_TestCase {

    public function testStemm()
    {
        $this->assertTrue(is_array(Mystem::stemm('тест')));
    }

    /**
     * @expectedException \Exception
     */
    public function testStemmNotFound()
    {
        Mystem::$mystemPath = '/WrongPath/';
        Mystem::stemm('тест');
    }

    public function testStemmNotRecreated()
    {
        Mystem::stemm('самолетами');
        $tStart = microtime( true );
        Mystem::stemm('пароходами');
        $tDiff = microtime( true ) - $tStart;
        $this->assertLessThan( 0.005, $tDiff, 'Took too long' );
    }

}