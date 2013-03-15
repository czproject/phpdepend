<?php
use Tester\Assert;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../PhpDepend.php';

$phpdepend = new Cz\PhpDepend;


// Empty PHP file
$phpdepend->parse("<?php");
Assert::same(array(), $phpdepend->getClasses());
Assert::same(array(), $phpdepend->getDependencies());


// Basic class definition
$phpdepend->parse("<?php
class MyClass
{
}
");

Assert::same(array('MyClass'), $phpdepend->getClasses());
Assert::same(array(), $phpdepend->getDependencies());


// Basic class definition in namespace
$phpdepend->parse("<?php
namespace Foo\\Bar;
class MyClass {}");

Assert::same(array('Foo\\Bar\\MyClass'), $phpdepend->getClasses());
Assert::same(array(), $phpdepend->getDependencies());



