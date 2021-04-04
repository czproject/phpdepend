<?php

/*
 * Bug https://github.com/czproject/phpdepend/issues/7
 */

use Tester\Assert;
require __DIR__ . '/bootstrap.php';

$phpdepend = new Cz\PhpDepend;


// Basic class dependencies
$phpdepend->parse('<?php
namespace MyName\Space;

class MyClass
{
	public function __construct()
	{
		$ret = \Bar::class;
		$set = \Foo::class;
		$get = \Bar\FooBar::class;
		$let = self::class;
		$met = static::class;
	}
}

echo MyClass::class;
');

Assert::same([
	'MyName\Space\MyClass',
], $phpdepend->getClasses());

Assert::same([
	'Bar',
	'Foo',
	'Bar\FooBar',
	'MyName\Space\MyClass',
], $phpdepend->getDependencies());
