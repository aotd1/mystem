<?php

use \Mystem\Word;
use \Mystem\MystemConst;

class BadWordsTest extends \PHPUnit_Framework_TestCase {

    public function testDictionaryWord()
    {
        $this->assertTrue(Word::stemm('бляди')->isBadWord());
    }

    public function testDictionaryWordNegative()
    {
        $this->assertFalse(Word::stemm('пончики')->isBadWord());
    }

    public function testFalseNegative()
    {
        Word::$falseNegativeList = array('пенчекряки');
        $this->assertTrue(Word::stemm('пенчекряки')->isBadWord());
        $this->assertFalse(Word::stemm('пенчекряк')->isBadWord());
    }

    public function testFalseNegativeNormalized()
    {
        Word::$falseNegativeNormalizedList = array('пенчекряк');
        $this->assertTrue(Word::stemm('пенчекряки')->isBadWord());
        $this->assertTrue(Word::stemm('пенчекряк')->isBadWord());
    }

    public function testFalseNegativeNormalizedInOpsitive()
    {
        Word::$falseNegativeNormalizedList = array('пенчекряк');
        Word::$falsePositiveList = array('пенчекряки');
        $this->assertFalse(Word::stemm('пенчекряки')->isBadWord());
        $this->assertTrue(Word::stemm('пенчекряков')->isBadWord());
        $this->assertTrue(Word::stemm('пенчекряк')->isBadWord());
    }

    public function testFalsePositive()
    {
        Word::$falsePositiveList = array('бляди');
        $this->assertFalse(Word::stemm('бляди')->isBadWord());
        $this->assertTrue(Word::stemm('блядь')->isBadWord());
    }

    public function testFalsePositiveNormalized()
    {
        Word::$falsePositiveNormalizedList = array('блядь');
        $this->assertFalse(Word::stemm('бляди')->isBadWord());
        $this->assertFalse(Word::stemm('блядь')->isBadWord());
    }

    public function testFalsePositiveNormalizedInNegative()
    {
        Word::$falsePositiveNormalizedList = array('блядь');
        Word::$falseNegativeList = array('бляди');
        $this->assertFalse(Word::stemm('блядь')->isBadWord());
        $this->assertFalse(Word::stemm('блядей')->isBadWord());
        $this->assertTrue(Word::stemm('бляди')->isBadWord());
    }

} 