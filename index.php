<?php
require 'src/Mystem/MystemConst.php';
require 'src/Mystem/Mystem.php';
require 'src/Mystem/Word.php';
require 'src/Mystem/ArticleWord.php';
require 'src/Mystem/Article.php';

$text = <<<BARMAGLOT
Варкалось. Хливкие шорьки
Пырялись по наве,
И хрюкотали зелюки,
Как мюмзики в мове.
BARMAGLOT;

$article = new \Mystem\Article($text);
echo "All words:\n";
foreach ($article->words as $word) {
    echo $word." ";
    var_dump($word);
}