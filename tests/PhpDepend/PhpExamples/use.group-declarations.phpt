<?php

/**
 * @phpversion >= 7.0
 */

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

// Group `use` declarations
// http://php.net/manual/en/language.namespaces.importing.php
$phpdepend->parse('<?php
use some\ns\{ClassA, ClassB, ClassC as C};
use function some\ns\{fn_a, fn_b, fn_c};
use const some\ns\{ConstA, ConstB, ConstC};

$a = new ClassA; // some\ns\ClassA
$a = new ClassB; // some\ns\ClassB
$a = new ClassC; // ClassC
$a = new C; // some\ns\ClassC
$a = new fn_a; // fn_a
$a = new fn_b; // fn_b
$a = new fn_c; // fn_c
$a = new ConstA; // ConstA
$a = new ConstB; // ConstB
$a = new ConstC; // ConstC
');

Assert::same([], $phpdepend->getClasses());
Assert::same([
	'some\ns\ClassA',
	'some\ns\ClassB',
	'ClassC',
	'some\ns\ClassC',
	'fn_a',
	'fn_b',
	'fn_c',
	'ConstA',
	'ConstB',
	'ConstC',
], $phpdepend->getDependencies());


// spaces
$phpdepend->parse('<?php
use some\ns\{ ClassA, ClassB, ClassC as C };

$a = new ClassA; // some\ns\ClassA
$a = new ClassB; // some\ns\ClassB
$a = new ClassC; // ClassC
$a = new C; // some\ns\ClassC
');

Assert::same([], $phpdepend->getClasses());
Assert::same([
	'some\ns\ClassA',
	'some\ns\ClassB',
	'ClassC',
	'some\ns\ClassC',
], $phpdepend->getDependencies());


// subpath
$phpdepend->parse('<?php
use A\B\{A, B\C, C as D};

$a = new A;
$a = new C;
$a = new D;
');

Assert::same([], $phpdepend->getClasses());
Assert::same([
	'A\B\A',
	'A\B\B\C',
	'A\B\C',
], $phpdepend->getDependencies());
