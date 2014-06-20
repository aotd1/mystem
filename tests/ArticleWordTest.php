<?php

use \Mystem\ArticleWord;

class ArticleWordTest extends \PHPUnit_Framework_TestCase {

    public function testLoad()
    {
        $article = 'Мы летели самолетами';
        $word = ArticleWord::stemm('самолетами', null, $article);
        $this->assertTrue($word instanceof ArticleWord);
    }

}