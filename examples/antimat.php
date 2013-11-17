<?php
require '../vendor/autoload.php';

$mayakovsky = <<<TEXT
Не те бляди,
что хлеба ради
спереди и сзади
дают нам ебти,
Бог их прости!

А те бляди - лгущие,
деньги сосущие,
еть не дающие -
вот бляди сущие,
мать их ети!
TEXT;

$article = new \Mystem\Article($mayakovsky);

//Проверяем текст на наличие обсценной лексики
var_dump($article->checkBadWords(false));

//Добавляем словарь нормализованных исключений
\Mystem\Article::$falsePositiveNormalizedList = array('блядь');
var_dump($article->checkBadWords(false));

//Добавляем словарь ложно-отрицательных прямых включений
\Mystem\Article::$falseNegativeList = array('ебти');
var_dump($article->checkBadWords(false));