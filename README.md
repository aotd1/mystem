Mystem
======

Описание
-----------

Простой wrapper для [Yandex mystem](http://company.yandex.ru/technologies/mystem/), - морфологического анализатора.
Работает исключительно с русской морфологией.

Установка
------------

### Composer ###
To install with composer add the following to your `composer.json` file:
```js
{
    "require": {
        "aotd/mystem": "dev-master"
    }
}
```
 
```bash
$ composer install
```

Использование
-----

Нормализуем все слова в тексте

```php
<?php
require "vendor/autoload.php";

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
```

Ищем маты

```php
<?php
require "vendor/autoload.php";

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
var_dump($article->checkBadWords(false));

//Добавляем словарь исключений
\Mystem\Article::setIgnoredBadList(array('блядь'));
var_dump($article->checkBadWords(false));
```

Время глагола

```php
<?php
require "vendor/autoload.php";

$verbs = array(
    'шедший',
    'идущий',
    'вычислявшийся',
    'вычисляющийся'
);

foreach ($verbs as $word) {
    echo $word . " - " . \Mystem\Word::stemm($word)->getVerbTime() . "\n";
}
```
