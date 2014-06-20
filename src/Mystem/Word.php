<?php
namespace Mystem;

/**
 * Class Word
 * @property array[] $variants lexical interpretation variants:
 *  - string $normalized - normalized word representation
 *  - boolean $strict - dictionary or predictable normalized representation
 *  - array $grammems - lexical information (constants from MystemConst)
 */
class Word
{
    /**
     * @var string $grammemeRegexp cached constants regular expression from MystemConst
     */
    private static $grammemeRegexp = null;

    /* @var string $constructorClass need for instantiate properly class in newFrom* methods */
    protected static $constructorClass = '\Mystem\Word';

    /**
     * @var string $original original string
     */
    public $original;

    public $variants = array();


    /* @var string[] $falsePositiveList */
    public static $falsePositiveList = array();

    /* @var string[] $falsePositiveList */
    public static $falsePositiveNormalizedList = array();

    /* @var string[] $falseNegativeList */
    public static $falseNegativeList = array();

    /* @var string[] $falseNegativeList */
    public static $falseNegativeNormalizedList = array();

    public function __construct()
    {
        if (self::$grammemeRegexp === null) {
            self::$grammemeRegexp = '#('.implode('|', MystemConst::grammemeList()).')#u';
        }
    }

    /**
     * @param string $lexicalString - prepared string from Mystem
     * @param int $maxVariants
     * @return Word
     */
    public static function newFromLexicalString($lexicalString, $maxVariants = null)
    {
        /* @var Word $word */
        $word = new static::$constructorClass();
        $word->parse($lexicalString, $maxVariants);
        return $word;
    }

    /**
     * @param string $word
     * @param int $maxVariants
     * @return Word
     */
    public static function stemm($word, $maxVariants = null)
    {
        $lexicalString = Mystem::stemm($word);
        return self::newFromLexicalString($lexicalString[0], $maxVariants);
    }

    /**
     * Normalized word
     * @return string
     */
    public function normalized()
    {
        if (isset($this->variants[0]['normalized'])) {
            return $this->variants[0]['normalized'];
        } else {
            return '';
        }
    }

    public function __toString(){
        return $this->normalized();
    }

    /**
     * Parse raw morphological data from mystem and fill Word object data
     * @param string $lexicalString - prepared string from Mystem
     * @param int $maxVariants
     */
    protected function parse($lexicalString, $maxVariants = null)
    {
        $counter = 0;
        $this->original = mb_substr($lexicalString, 0, mb_strpos($lexicalString, '{'));
        $variants = explode('|', mb_substr($lexicalString, mb_strlen($this->original) + 1, -1));
        foreach ($variants as $text)
        {
            preg_match('#^(?P<normalized>[^=]*)=(?P<grammems>.*)$#u', $text, $match);
            $variant['normalized'] = !empty($match['normalized']) ? $match['normalized'] : $this->normalized();
            if (mb_strrpos($variant['normalized'], '?')) {
                $variant['strict'] = false;
                $variant['normalized'] = mb_substr($variant['normalized'], 0, -1);
            } else {
                $variant['strict'] = true;
            }

            if (!empty($match['grammems'])) {
                $gramm = strtr($match['grammems'], '=,', '  ');
                preg_match_all(self::$grammemeRegexp, $gramm, $match);
                if (!empty($match[0]))
                    $variant['grammems'] = $match[0];
            } else {
                $variant['grammems'] = array();
            }
            $this->variants[$counter++] = $variant;
            if ($maxVariants!==null && $counter>=$maxVariants)
                break;
        }
    }

    /**
     * Search grammese primitive in word variants
     * @param $gramm - grammar primitive from MystemConst
     * @param null $level - variants maximum depth
     * @return boolean
     */
    public function checkGrammeme($gramm, $level = null){
        $counter = 0;
        foreach ($this->variants as $variant) {
            if (in_array($gramm, $variant['grammems'])) {
                return true;
            } elseif ($level!==null && ++$counter>=$level) {
                return false;
            }
        }
        return false;
    }

    /**
     * Get verb time: present, past or future
     * @param int $variant find in which morphological variant
     * @return null | MystemConst::PRESENT | MystemConst::PAST | MystemConst::FUTURE
     */
    public function getVerbTime($variant = 0)
    {
        return $this->searchGrammemeInList(array(
            MystemConst::PRESENT, MystemConst::FUTURE, MystemConst::PAST
        ), $variant);
    }

    /**
     * Get count: single or plural
     * @param int $variant find in which morphological variant
     * @return null | string - MystemConst
     */
    public function getCount($variant = 0)
    {
        return $this->searchGrammemeInList(array(
            MystemConst::SINGULAR, MystemConst::PLURAL
        ), $variant);
    }

    /**
     * Get gender
     * @param int $variant find in which morphological variant
     * @return null | string - MystemConst
     */
    public function getGender($variant = 0)
    {
        return $this->searchGrammemeInList(array(
            MystemConst::FEMININE, MystemConst::MASCULINE, MystemConst::NEUTER
        ), $variant);
    }

    /**
     * Get animate
     * @param int $variant find in which morphological variant
     * @return null | string - MystemConst
     */
    public function getAnimate($variant = 0)
    {
        return $this->searchGrammemeInList(array(
            MystemConst::ANIMATE, MystemConst::INANIMATE
        ), $variant);
    }

    /**
     * Get noun case
     * @param int $variant
     * @return null | string - MystemConst
     */
    public function getNounCase($variant = 0)
    {
        return $this->searchGrammemeInList(array(
            MystemConst::NOMINATIVE,
            MystemConst::GENITIVE,
            MystemConst::DATIVE,
            MystemConst::ACCUSATIVE,
            MystemConst::INSTRUMENTAL,
            MystemConst::PREPOSITIONAL,
            MystemConst::PARTITIVE,
            MystemConst::LOCATIVE,
            MystemConst::VOCATIVE,
        ), $variant);
    }

    protected function searchGrammemeInList(array $constants, $variant = 0)
    {
        if (!isset($this->variants[$variant])) {
            return null;
        }

        foreach ($constants as $grammeme) {
            if (in_array($grammeme, $this->variants[$variant]['grammems'])) {
                return $grammeme;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isBadWord()
    {
        $original = mb_strtolower($this->original, 'UTF-8');
        if ($this->checkGrammeme(MystemConst::OTHER_VULGARISM)) {
            return !in_array($original, self::$falsePositiveList) &&
            !in_array($this->normalized(), self::$falsePositiveNormalizedList);
        } else {
            return in_array($original, self::$falseNegativeList) ||
            in_array($this->normalized(), self::$falseNegativeNormalizedList);
        }
    }

}