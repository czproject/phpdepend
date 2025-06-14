<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;


// Existing file
Assert::true($phpdepend->parseFile(__DIR__ . '/basic.inc.php'));
Assert::same(['MyClass'], $phpdepend->getClasses());


// Missing file
Assert::false(@$phpdepend->parseFile(__DIR__ . '/bad.file.php')); // @ - intentionally
