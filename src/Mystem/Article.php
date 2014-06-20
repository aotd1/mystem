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
            if ($word->isBadWord()) {
                $result[$word->original] = $word->normalized();
                if ($stopOnFirst)
                    break;
            }
        }
        return $result;
    }

}
