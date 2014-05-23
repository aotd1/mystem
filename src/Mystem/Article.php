<?php
namespace Mystem;

/**
 * Class MystemArticle
 */
class Article
{
    /* @var string $article */
    public $article = '';

    /* @var ArticleWord[] $words */
    public $words = array();

    /* @var string[] $falsePositiveList */
    public static $falsePositiveList = array();

    /* @var string[] $falsePositiveList */
    public static $falsePositiveNormalizedList = array();

    /* @var string[] $falseNegativeList */
    public static $falseNegativeList = array();

    /* @var string[] $falseNegativeList */
    public static $falseNegativeNormalizedList = array();

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $offset = 0;
        $this->article = $text;

        $stemmed = Mystem::stemm($text);
        foreach ($stemmed as $part) {
            $word = ArticleWord::newFromLexicalString($part, 1, $this->article);
            $position = @mb_strpos($this->article, $word->original, $offset);
            if ($position === false) //Can't find original word
                $position = $offset + 1;
            $word->position = $position;
            $offset = $word->position + mb_strlen($word->original);
            $this->words[] = $word;
        }
    }

    /**
     * @return string
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param bool $stopOnFirst
     * @return array
     */
    public function checkBadWords($stopOnFirst = false)
    {
        $result = array();
        foreach ($this->words as &$word) {
            if (self::isBadWord($word)) {
                $result[$word->original] = $word->normalized();
                if ($stopOnFirst)
                    break;
            }
        }
        return $result;
    }

    /**
     * @param Word $word
     * @return bool
     * @todo: move isBadWord in Word class
     */
    protected static function isBadWord(Word $word)
    {
        $original = mb_strtolower($word->original, 'UTF-8');
        if ($word->checkGrammeme(MystemConst::OTHER_VULGARISM)) {
            $inExceptions = in_array($original, self::$falsePositiveList) &&
                in_array($word->normalized(), self::$falsePositiveNormalizedList);
            if ($inExceptions) {
                $word->removeGrammeme(MystemConst::OTHER_VULGARISM);
            }
            return !$inExceptions;
        } else {
            $inExceptions = in_array($original, self::$falseNegativeList) ||
                in_array($word->normalized(), self::$falseNegativeNormalizedList);
            if ($inExceptions) {
                $word->addGrammeme(MystemConst::OTHER_VULGARISM);
            }
            return $inExceptions;
        }
    }

}
