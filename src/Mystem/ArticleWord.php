<?php
namespace Mystem;

class ArticleWord extends Word {

    protected static $constructorClass = '\Mystem\ArticleWord';

    protected $article;

    public $position;

    /**
     * @param string $lexicalString
     * @param int $maxVariants
     * @param string $article
     * @return ArticleWord
     */
    public static function newFromLexicalString($lexicalString, $maxVariants = null, &$article = null){
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
    public static function stemm($word, $maxVariants = null, &$article = null){
        /* @var ArticleWord $word */
        $word = parent::stemm($word, $maxVariants);
        if ($article !== null) {
            $word->article = &$article;
        }
        return $word;
    }
}