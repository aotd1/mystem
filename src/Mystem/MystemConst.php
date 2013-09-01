<?php
namespace Mystem;

/**
 * Class MystemConst
 * Lexical information constants
 */
class MystemConst
{
    private static $grammems = null;

    //Части речи
    const PART_A = 'A'; //прилагательное
    const PART_ADV = 'ADV';//наречие
    const PART_ADVPRO = 'ADVPRO';//местоименное наречие
    const PART_ANUM = 'ANUM';//порядковое числительное
    const PART_APRO = 'APRO';//местоименное прилагательное
    const PART_COM = 'COM';//часть композита
    const PART_CONJ = 'CONJ';//союз
    const PART_INTJ = 'INTJ';//междометие
    const PART_NUM = 'NUM';//числительное
    const PART_PART = 'PART';//частица
    const PART_PR = 'PR';//предлог
    const PART_S = 'S';//существительное
    const PART_SPRO = 'SPRO';//местоимение
    const PART_V = 'V';//глагол

    //Время глаголов
    const PRESENT = 'наст'; //настоящее
    const FUTURE = 'непрош'; //непрошедшее
    const PAST = 'прош'; //прошедшее

    //Падеж
    const NOMINATIVE = 'им';
    const GENITIVE = 'род';
    const DATIVE = 'дат';
    const ACCUSATIVE = 'вин';
    const INSTRUMENTAL = 'твор';
    const PREPOSITIONAL = 'пр';
    const PARTITIVE = 'парт';
    const LOCATIVE = 'местн';
    const VOCATIVE = 'зват';

    //Count
    const SUNGULAR = 'ед';
    const PLURAL = 'мн';

    //Репрезентация и наклонение глагола
    const VERBAL_ADV = 'деепр';
    const INFINITIVE = 'инф';
    const PARTICIPLE = 'прич';
    const INDICATIVE = 'изъяв';
    const IMPERATIVE = 'пов';

    //Форма прилагательных
    const SHORT = 'кр';
    const FULL = 'полн';
    const POSSESIVE = 'притяж';

    //Степень сравнения
    const SUPERLATIVE = 'прев';
    const COMPARATIVE = 'срав';

    //Род
    const FEMININE = 'жен';
    const MASCULINE = 'муж';
    const NEUTER = 'сред';

    //Вид (аспект) глагола
    const PERFECT = 'сов';
    const IMPERFECT = 'несов';

    //Залог
    const ACTIVE = 'действ';
    const PASSIVE = 'страд';

    //Одушевленность
    const ANIMATE = 'од';
    const INANIMATE = 'неод';

    //Переходность
    const TRANSITIVE = 'пе';
    const NONTRANSITIVE = 'нп';

    //Прочие обозначения
    const OTHER_PARENTHESIS = 'вводн';
    const OTHER_GEO ='гео';
    const OTHER_WTF = 'затр';
    const OTHER_NAME = 'имя';
    const OTHER_CORRUPT = 'искаж';
    const OTHER_MF = 'мж';
    const OTHER_VULGARISM = 'обсц';
    const OTHER_SEC_NAME = 'отч';
    const OTHER_PREDICTIVE = 'прдк';
    const OTHER_COLLOQUIAL = 'разг';
    const OTHER_RARE = 'редк';
    const OTHER_ABBREVIATION = 'сокр';
    const OTHER_OUTDATED = 'устар';
    const OTHER_LAST_NAME = 'фам';

    public static function grammemeList(){
        if (self::$grammems === null) {
            $class = new \ReflectionClass ('\Mystem\MystemConst');
            self::$grammems = $class->getConstants();
            unset($const);
        }
        return self::$grammems;
    }
}