<?php
use Tester\Assert;
require __DIR__ . '/bootstrap.php';

$phpdepend = new Cz\PhpDepend;

$phpdepend->parse('<?php
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

namespace # global namespace
{
	class MyGlobalClass extends NS1\NS3\ParentClass2
	{
	}
}
');

Assert::same([
	'NS1\MyClass',
	'NS2\MyClass2',
	'NS3\MyClass3',
	'MyGlobalClass',
], $phpdepend->getClasses());

Assert::same([
	'NS1\MyInterface',
	'NS2\NS3\ParentClass',
	'NS4\NS5\NS7\ParentClass',
	'NS4\NS5\NS6\FooInterface',
	'NS1\NS3\ParentClass2',
], $phpdepend->getDependencies());



$phpdepend->parse('<?php
namespace NFirst;
	class MyClass implements MyInterface
	{
	}

namespace NSecond;
	class MyClass2 extends NThird\ParentClass
	{
	}

namespace NThird;
	use NS4\NS5\NS6;
	use NS4\NS5\NS7 as NS9;

	class MyClass3 extends NS9\ParentClass implements NS6\FooInterface
	{
	}

namespace; # global namespace
	class MyGlobalClass extends NS1\NS3\ParentClass2
	{
	}
');

Assert::same([
	'NFirst\MyClass',
	'NSecond\MyClass2',
	'NThird\MyClass3',
	'MyGlobalClass',
], $phpdepend->getClasses());

Assert::same([
	'NFirst\MyInterface',
	'NSecond\NThird\ParentClass',
	'NS4\NS5\NS7\ParentClass',
	'NS4\NS5\NS6\FooInterface',
	'NS1\NS3\ParentClass2',
], $phpdepend->getDependencies());
