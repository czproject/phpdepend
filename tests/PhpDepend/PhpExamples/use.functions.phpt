<?php

/**
 * @phpversion >= 5.6
 */

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

// http://php.net/manual/en/language.namespaces.importing.php
$phpdepend->parse('<?php
use function some\ns\fn_a;

$a = new fn_a;
');

Assert::same([], $phpdepend->getClasses());
Assert::same([
	'fn_a',
], $phpdepend->getDependencies());


// grouped
$phpdepend->parse('<?php
use function NS\{funcA, funcB};

$a = new funcA;
$a = new funcB;
');

Assert::same([], $phpdepend->getClasses());
Assert::same([
	'funcA',
	'funcB',
], $phpdepend->getDependencies());
