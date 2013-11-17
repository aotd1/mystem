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
     * @param string[] $ignoredBadList
     */
    public function __construct($text, array $ignoredBadList = array())
    {
        $offset = 0;
        $this->article = $text;

        if (!empty($ignoredBadList)) {
            $this->setIgnoredBadList($ignoredBadList);
        }

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
        foreach ($this->words as $word) {
            if ($this->isBadWord($word)) {
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
     */
    protected static function isBadWord(Word $word)
    {
        $original = mb_strtolower($word->original, 'UTF-8');
        if ($word->checkGrammeme(MystemConst::OTHER_VULGARISM)) {
            return !in_array($original, self::$falsePositiveList) &&
                   !in_array($word->normalized(), self::$falsePositiveNormalizedList);
        } else {
            return in_array($original, self::$falseNegativeList) ||
                   in_array($word->normalized(), self::$falseNegativeNormalizedList);
        }
    }

}
