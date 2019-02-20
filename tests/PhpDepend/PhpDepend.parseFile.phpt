<?php
use Tester\Assert;
require __DIR__ . '/bootstrap.php';

$phpdepend = new Cz\PhpDepend;


// Existing file
Assert::true($phpdepend->parseFile(__DIR__ . '/basic.inc.php'));
Assert::same(array('MyClass'), $phpdepend->getClasses());


// Missing file
Assert::false(@$phpdepend->parseFile(__DIR__ . '/bad.file.php')); // @ - intentionally
