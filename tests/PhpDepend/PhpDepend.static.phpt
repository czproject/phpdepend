<?php
use Tester\Assert;
require __DIR__ . '/bootstrap.php';

$phpdepend = new Cz\PhpDepend;



$phpdepend->parse('<?php
class MyClass
{
	public function __construct()
	{
		$ret = self::fooBar();
		$set = static::fooBar();
		$get = parent::fooBar();
	}
}
');

Assert::same(['MyClass'], $phpdepend->getClasses());
Assert::same([], $phpdepend->getDependencies());



$phpdepend->parse('<?php
use Foo\Bar;

class MyClass
{
	public function __construct()
	{
		$ret = Bar::fooBar();
		$set = Foo::fooBar();
		$get = Bar\FooBar::fooBar();
	}
}
');

Assert::same(['MyClass'], $phpdepend->getClasses());
Assert::same(['Foo\Bar', 'Foo', 'Foo\Bar\FooBar'], $phpdepend->getDependencies());
