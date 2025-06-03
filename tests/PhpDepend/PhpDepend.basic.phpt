<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;


// Empty PHP file
$phpdepend->parse('<?php');
Assert::same([], $phpdepend->getClasses());
Assert::same([], $phpdepend->getDependencies());


// Basic class definition
$phpdepend->parse('<?php
class MyClass
{
}
');

Assert::same(['MyClass'], $phpdepend->getClasses());
Assert::same([], $phpdepend->getDependencies());


// Basic class definition in namespace
$phpdepend->parse('<?php
namespace Foo\\Bar;
class MyClass {}');

Assert::same(['Foo\\Bar\\MyClass'], $phpdepend->getClasses());
Assert::same([], $phpdepend->getDependencies());


// Class definition
$phpdepend->parse('<?php
namespace Foo\\Bar;
class MyClass extends namespace\MyParent implements namespace\MyInterface {

}
');

Assert::same(['Foo\\Bar\\MyClass'], $phpdepend->getClasses());
Assert::same([
	'Foo\Bar\MyParent',
	'Foo\Bar\MyInterface',
], $phpdepend->getDependencies());
