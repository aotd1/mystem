<?php
require '../vendor/autoload.php';

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
}
