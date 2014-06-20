<?php

use \Mystem\Article;

class ArticleTest extends \PHPUnit_Framework_TestCase {

    public function testLoad()
    {
        $article = new Article('На дворе смеркалось');
        $this->assertCount(3, $article->words);
    }

    public function testGetArticle()
    {
        $article = new Article('Ёжик в тумане');
        $this->assertTrue(is_string($article->getArticle()));
    }

    public function testCheckBadWords()
    {
        $article = new Article('Не те бляди, что денег ради…');
        $this->assertNotEmpty($article->checkBadWords());
        $this->assertNotEmpty($article->checkBadWords(true));
    }

    public function testCheckNoBadWords()
    {
        $article = new Article('Однажды лебедь, рак и щука…');
        $this->assertEmpty($article->checkBadWords());
    }

}