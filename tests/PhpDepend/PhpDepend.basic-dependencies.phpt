<?php
use Tester\Assert;
require __DIR__ . '/bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;


// Basic class dependencies
$phpdepend->parse('<?php
$foo = new Foo;
$bar = new Bar;
');

Assert::same([], $phpdepend->getClasses());
Assert::same(['Foo', 'Bar'], $phpdepend->getDependencies());


// Basic class definition in namespace
$phpdepend->parse('<?php
namespace Foo\\Bar;
$foo = new Foo;
$bar = new Bar;
$barfoo = new namespace\BarFoo;
');

Assert::same([], $phpdepend->getClasses());
Assert::same([
	'Foo\\Bar\\Foo',
	'Foo\\Bar\\Bar',
	'Foo\\Bar\\BarFoo',
], $phpdepend->getDependencies());
