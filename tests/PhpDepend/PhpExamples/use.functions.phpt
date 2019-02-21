<?php
/**
 * @phpversion >= 5.6
 */
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';

$phpdepend = new Cz\PhpDepend;

// http://php.net/manual/en/language.namespaces.importing.php
$phpdepend->parse('<?php
use function some\ns\fn_a;

$a = new fn_a;
');

Assert::same(array(), $phpdepend->getClasses());
Assert::same(array(
	'fn_a',
), $phpdepend->getDependencies());


// grouped
$phpdepend->parse('<?php
use function NS\{funcA, funcB};

$a = new funcA;
$a = new funcB;
');

Assert::same(array(), $phpdepend->getClasses());
Assert::same(array(
	'funcA',
	'funcB',
), $phpdepend->getDependencies());
