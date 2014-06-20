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

}