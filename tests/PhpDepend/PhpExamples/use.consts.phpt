<?php

declare(strict_types=1);

/**
 * @phpversion >= 5.6
 */

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

// http://php.net/manual/en/language.namespaces.importing.php
$phpdepend->parse('<?php
use const My\Full\CONSTANT;

$a = new CONSTANT;
');

Assert::same([], $phpdepend->getClasses());
Assert::same([
	'CONSTANT',
], $phpdepend->getDependencies());
