[![unitTests](https://travis-ci.org/aotd1/mystem.svg?branch=master)](https://travis-ci.org/aotd1/mystem)
[![Code Climate](https://codeclimate.com/github/aotd1/mystem.png)](https://codeclimate.com/github/aotd1/mystem)
[![Total Downloads](https://poser.pugx.org/aotd/mystem/downloads.svg)](https://packagist.org/packages/aotd/mystem)
[![License](https://poser.pugx.org/aotd/mystem/license.svg)](https://packagist.org/packages/aotd/mystem)

Использование Mystem
====================

Простая обертка для [Yandex mystem](http://company.yandex.ru/technologies/mystem/).
Работает исключительно с русской морфологией.

Разрабатывалась для определения ненормативной лексики в текстах, но вполне подойдет и для стемминга и морфологического анализа.

Установка
---------

Библиотека доступна в Packagist ([aotd/mystem](http://packagist.org/packages/aotd/mystem)) и устанавливается через [Composer](http://getcomposer.org/).

```bash
php composer.phar require aotd/mystem 'dev-master'
```

Никто не запрещает просто скачать исходники с GitHub и использовать любой PSR-0 автолоадер.

Использование
-------------

Все примеры собраны в папке examples.

 - antimat - проверка текста на наличие обсценной лексики.
 - jabberwocky - стемминг части стихотворения «Бармаглот» (в переводе Дины Орловской).
 - verb-tense - определение времени глагола

### Использование с Yii ###

Устанавливаем библиотеку через composer

```js
{
    "require": {
        "aotd/mystem": "dev-master"
    },
    "scripts": {
        "post-install-cmd": [ "MystemBinaryInstaller::install" ],
        "post-update-cmd":  [ "MystemBinaryInstaller::update" ]
    },
}
```

```bash
$ composer install
```

Секция scripts необходима для установки бинарного файла mystem от Яндекса
в зависимости от целевой платформы (Win/Mac OS/Linux/FreeBSD)


Складываем `ExtMystem.php` в `/protected/extensions/Mystem`, добавлеям в конфиг в секцию `components`:

```php
    ...
    'mystem' => array(
        'class' => 'ext.Mystem.ExtMystem',
//      'falsePositive' => __DIR__ . '/mystem/false-positive.txt',
//      'falsePositiveNormalized' => __DIR__ . '/mystem/false-positive-normalized.txt',
//      'falseNegative' => __DIR__ . '/mystem/false-negative.txt',
//      'falseNegativeNormalized' => __DIR__ . '/mystem/false-negative-normalized.txt',
    ),
    ...
```

Опционально указываем списки ложно-положительных, ложно-отрицательных слов для фильтра обсценной лексики...

```php
    Yii::app()->mystem->checkArticle('Текст для проверки на наличие матов');
```

Profit!

P.S. Никто не отменяет великость и могучесть русского языка, потому всецело доверять такому решению не стоит :)