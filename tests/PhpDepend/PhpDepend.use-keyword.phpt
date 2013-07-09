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

