<?php
use Tester\Assert;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/PhpDepend.php';

$phpdepend = new Cz\PhpDepend;


// Basic class dependencies
$phpdepend->parse("<?php
\$foo = new Foo;
\$bar = new Bar;
");

Assert::same(array(), $phpdepend->getClasses());
Assert::same(array('Foo', 'Bar'), $phpdepend->getDependencies());


// Basic class definition in namespace
$phpdepend->parse("<?php
namespace Foo\\Bar;
\$foo = new Foo;
\$bar = new Bar;
");

Assert::same(array(), $phpdepend->getClasses());
Assert::same(array(
	'Foo\\Bar\\Foo',
	'Foo\\Bar\\Bar',
), $phpdepend->getDependencies());



