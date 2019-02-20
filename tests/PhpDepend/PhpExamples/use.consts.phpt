<?php
/**
 * @phpversion >= 5.6
 */
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';

$phpdepend = new Cz\PhpDepend;

// http://php.net/manual/en/language.namespaces.importing.php
$phpdepend->parse('<?php
use const My\Full\CONSTANT;

$a = new CONSTANT;
');

Assert::same(array(), $phpdepend->getClasses());
Assert::same(array(
	'CONSTANT',
), $phpdepend->getDependencies());
