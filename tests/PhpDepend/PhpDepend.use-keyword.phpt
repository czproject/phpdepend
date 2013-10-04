<?php
use Tester\Assert;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/PhpDepend.php';

$phpdepend = new Cz\PhpDepend;



$phpdepend->parse("<?php
use MyNamespace\Sub\SubClass;

class MyClass implements SubClass
{
}
");

Assert::same(array('MyClass'), $phpdepend->getClasses());
Assert::same(array('MyNamespace\Sub\SubClass'), $phpdepend->getDependencies());


// multiuse
$phpdepend->parse("<?php
use NS4\NS5\NS6;
use NS4\NS5\NS7 as NS9;

class MyClass3 extends NS9\ParentClass implements NS6\FooInterface
{
}
");

Assert::same(array('MyClass3'), $phpdepend->getClasses());
Assert::same(array(
	'NS4\NS5\NS7\ParentClass',
	'NS4\NS5\NS6\FooInterface',
), $phpdepend->getDependencies());
