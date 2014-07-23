<?php

use \Mystem\Word;
use \Mystem\MystemConst;

class WordTest extends \PHPUnit_Framework_TestCase {

    public function testStemm()
    {
        $word = Word::stemm('самолетами');
        $this->assertEquals('самолет', $word->normalized());
    }

    public function testFromLexicalString()
    {
        $lex = 'самолетами{самолет=S,муж,неод=твор,мн}';
        $word = Word::stemm($lex);
        $this->assertEquals('самолет', $word->normalized());
    }

    public function testCantNormalize()
    {
        $this->assertEmpty(Word::stemm('asfd')->normalized());
    }

    public function testToString()
    {
        $this->assertEquals('самолет', (string)Word::stemm('самолетами'));
    }

    public function testPredict()
    {
        $this->assertEquals('варкаться', Word::stemm('варкалось'));
    }

    public static function providerVerbTime()
    {
        return array(
            array('летел', MystemConst::PAST),
            array('полетит', MystemConst::FUTURE),
        );
    }

    /**
     * @dataProvider providerVerbTime
     * @param string $verb
     * @param string $time
     */
    public function testVerbTime($verb, $time)
    {
        $this->assertEquals($time, Word::stemm($verb)->getVerbTime());
    }

    public static function providerCount()
    {
        return array(
            array('ёжик', MystemConst::SINGULAR),
            array('ёжики', MystemConst::PLURAL),
            array('бегал', MystemConst::SINGULAR),
            array('бежали', MystemConst::PLURAL),
        );
    }

    /**
     * @dataProvider providerCount
     * @param string $noun
     * @param string $count
     */
    public function testCount($noun, $count)
    {
        $this->assertEquals($count, Word::stemm($noun)->getCount());
    }

    public static function providerGender()
    {
        return array(
            array('котейка', MystemConst::FEMININE),
            array('каравай', MystemConst::MASCULINE),
            array('ведро', MystemConst::NEUTER),
        );
    }

    /**
     * @dataProvider providerGender
     * @param string $noun
     * @param string $gender
     */
    public function testGender($noun, $gender)
    {
        $this->assertEquals($gender, Word::stemm($noun)->getGender());
    }

    public static function providerAnimate()
    {
        return array(
            array('поросенок', MystemConst::ANIMATE),
            array('стул', MystemConst::INANIMATE),
        );
    }

    /**
     * @dataProvider providerAnimate
     * @param string $noun
     * @param string $animate
     */
    public function testAnimate($noun, $animate)
    {
        $this->assertEquals($animate, Word::stemm($noun)->getAnimate());
    }

    public static function providerNounCase()
    {
        return array(
            array('прокурор', MystemConst::NOMINATIVE),
            array('прокуроров', MystemConst::ACCUSATIVE),
            array('прокурорам', MystemConst::DATIVE),
            array('прокурором', MystemConst::INSTRUMENTAL),
            array('прокуроре', MystemConst::PREPOSITIONAL),
        );
    }

    /**
     * @dataProvider providerNounCase
     * @param string $noun
     * @param string $case
     */
    public function testNounCase($noun, $case)
    {
        $this->assertEquals($case, Word::stemm($noun)->getNounCase());
    }

    public function testUndefinedGrammeme()
    {
        $this->assertNull(Word::stemm('летел')->getNounCase());
    }

    public function testCheckGrammeme()
    {
        $word = Word::stemm('банка');
        $this->assertTrue($word->checkGrammeme(MystemConst::FEMININE));
        $this->assertTrue($word->checkGrammeme(MystemConst::MASCULINE));
        $this->assertFalse($word->checkGrammeme(MystemConst::FEMININE, 1));
    }

    public function testNoVariantsWord()
    {
        $word = Word::stemm('ololo');
        $this->assertFalse($word->checkGrammeme(MystemConst::DATIVE));
        $this->assertNull($word->getNounCase(1));
    }

}