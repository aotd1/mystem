PHP Mystem wrapper
=============================

Description
-----------

Simple PHP wrapper for [Yandex mystem](http://company.yandex.ru/technologies/mystem/) morphological analysis program.
Russian morphology only!

Installation
------------

### Composer ###
To install with composer add the following to your `composer.json` file:
```js
{
    "require": {
        "aotd/Mystem": ">=0.9"
    }
}
```
git 
```bash
$ composer install
```

Usage
-----

Check bad words in text

```php
<?php
require 'Mystem.php';

Mystem::setIgnoredBadList(array_filter(explode("\n", file_get_contents('dictionaries/stop-words.txt')), 'trim'));

$article = new Mystem(file_get_contents('tests/simple.txt'));

$badWords = $article->checkBadWords(false);
if( sizeof($badWords)>0 ) {
    var_dump($badWords);
} else {
    echo "All clear\n";
}
```

Get verb time

```php
<?php
require 'Mystem.php';

$verb = new StemmedWord('убежавшими');
echo $verb->getVerbTime();
```


