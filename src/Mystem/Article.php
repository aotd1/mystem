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

    /* @var string[] $ignoredBadList */
    protected static $ignoredBadList = array();

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
            $position = mb_strpos($this->article, $word->original, $offset);
            if ($position === false) //Can't find original word
                $position = $offset + 1;
            $word->position = $position;
            $offset = $word->position + mb_strlen($word->original);
            $this->words[] = $word;
        }
    }

    /**
     * @param string[] $ignoredBadList
     * @param bool $reset
     */
    public static function setIgnoredBadList(array $ignoredBadList, $reset = false)
    {
        if ($reset || empty(self::$ignoredBadList))
            self::$ignoredBadList = $ignoredBadList;
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
        foreach ($this->words as $word)
            if ($word->checkGrammem(MystemConst::OTHER_VULGARISM) &&
                !in_array($word->normalized(), self::$ignoredBadList)
            ) {
                $result[$word->original] = $word->normalized();
                if ($stopOnFirst)
                    break;
            }
        return $result;
    }

}
