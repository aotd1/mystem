<?php
require '../vendor/autoload.php';

$verbs = array(
    'шедший',
    'идущий',
    'вычислявшийся',
    'вычисляющийся'
);

foreach ($verbs as $word) {
    echo $word . " - " . \Mystem\Word::stemm($word)->getVerbTime() . "\n";
}
