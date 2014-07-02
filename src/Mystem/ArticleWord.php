<?php
namespace Mystem;

/**
 * Class ArticleWord
 *
 * Initialized from Article class, has link to original article and $position, filled with in Article position
 * @package Mystem
 */
class ArticleWord extends Word
{

    protected static $constructorClass = '\Mystem\ArticleWord';

    /**
     * @var string $article link to original article string
     */
    protected $article;

    public $position;

    /**
     * @param string $lexicalString
     * @param int $maxVariants
     * @param string $article
     * @return ArticleWord
     */
    public static function newFromLexicalString($lexicalString, $maxVariants = null, &$article = null)
    {
        /* @var ArticleWord $word */
        $word = parent::newFromLexicalString($lexicalString, $maxVariants);
        if ($article !== null) {
            $word->article = &$article;
        }
        return $word;
    }

    /**
     * @param string $word
     * @param int $maxVariants
     * @param string $article
     * @return ArticleWord
     */
    public static function stemm($word, $maxVariants = null, &$article = null)
    {
        /* @var ArticleWord $word */
        $word = parent::stemm($word, $maxVariants);
        if ($article !== null) {
            $word->article = &$article;
        }
        return $word;
    }
}
