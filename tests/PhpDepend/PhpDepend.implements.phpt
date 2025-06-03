<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;


// Basic class & interface definition
$phpdepend->parse('<?php

interface IMyInterface
{
}

class MyClass implements IMyInterface
{
}
');

Assert::same(['IMyInterface', 'MyClass'], $phpdepend->getClasses());
Assert::same(['IMyInterface'], $phpdepend->getDependencies());



$phpdepend->parse('<?php
use Foo\Bar;

interface IMyInterface extends Bar\FooBar
{
}

class MyClass extends Foo\Bar\Object implements IMyInterface
{
}
');

Assert::same(['IMyInterface', 'MyClass'], $phpdepend->getClasses());
Assert::same([
	'Foo\Bar\FooBar', 'Foo\Bar\Object', 'IMyInterface'
], $phpdepend->getDependencies());
