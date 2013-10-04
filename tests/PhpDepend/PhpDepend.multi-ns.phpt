<?php
use Tester\Assert;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/PhpDepend.php';

$phpdepend = new Cz\PhpDepend;



$phpdepend->parse("<?php
namespace NS1
{
	class MyClass implements MyInterface
	{
	}
}

namespace NS2
{
	class MyClass2 extends NS3\ParentClass
	{
	}
}

namespace NS3
{
	use NS4\NS5\NS6;
	use NS4\NS5\NS7 as NS9;

	class MyClass3 extends NS9\ParentClass implements NS6\FooInterface
	{
	}
}
");

Assert::same(array(
	'NS1\MyClass',
	'NS2\MyClass2',
	'NS3\MyClass3',
), $phpdepend->getClasses());

Assert::same(array(
	'NS1\MyInterface',
	'NS2\NS3\ParentClass',
	'NS4\NS5\NS7\ParentClass',
	'NS4\NS5\NS6\FooInterface',
), $phpdepend->getDependencies());
