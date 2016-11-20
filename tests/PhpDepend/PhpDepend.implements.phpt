<?php
use Tester\Assert;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/PhpDepend.php';

$phpdepend = new Cz\PhpDepend;


// Basic class & interface definition
$phpdepend->parse('<?php

interface IMyInterface
{
}

class MyClass implements IMyInterface
{
}
');

Assert::same(array('IMyInterface', 'MyClass'), $phpdepend->getClasses());
Assert::same(array('IMyInterface'), $phpdepend->getDependencies());



$phpdepend->parse('<?php
use Foo\Bar;

interface IMyInterface extends Bar\FooBar
{
}

class MyClass extends Foo\Bar\Object implements IMyInterface
{
}
');

Assert::same(array('IMyInterface', 'MyClass'), $phpdepend->getClasses());
Assert::same(array(
	'Foo\Bar\FooBar', 'Foo\Bar\Object', 'IMyInterface'
), $phpdepend->getDependencies());
