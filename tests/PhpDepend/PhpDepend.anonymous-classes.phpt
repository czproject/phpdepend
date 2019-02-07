<?php

/**
 * @phpversion >= 5.4
 */

/*
 * http://php.net/manual/en/language.oop5.anonymous.php
 */

use Tester\Assert;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/PhpDepend.php';

$phpdepend = new Cz\PhpDepend;


/////////////////////////
$phpdepend->parse('<?php

// Pre PHP 7 code
class Logger
{
	public function log($msg)
	{
		echo $msg;
	}
}

$util->setLogger(new Logger());

// PHP 7+ code
$util->setLogger(new class {
	public function log($msg)
	{
		echo $msg;
	}
});
');

Assert::same(array('Logger'), $phpdepend->getClasses());
Assert::same(array('Logger'), $phpdepend->getDependencies());


/////////////////////////
$phpdepend->parse('<?php

class SomeClass {}
interface SomeInterface {}
trait SomeTrait {}

var_dump(new class(10) extends SomeClass implements SomeInterface {
	private $num;

	public function __construct($num)
	{
		$this->num = $num;
	}

	use SomeTrait;
});
');

Assert::same(array(
	'SomeClass',
	'SomeInterface',
	'SomeTrait',
), $phpdepend->getClasses());

Assert::same(array(
	'SomeClass',
	'SomeInterface',
	'SomeTrait',
), $phpdepend->getDependencies());


/////////////////////////
$phpdepend->parse('<?php

class Outer
{
	private $prop = 1;
	protected $prop2 = 2;

	protected function func1()
	{
		return 3;
	}

	public function func2()
	{
		return new class($this->prop) extends Outer {
			private $prop3;

			public function __construct($prop)
			{
				$this->prop3 = $prop;
			}

			public function func3()
			{
				return $this->prop2 + $this->prop3 + $this->func1();
			}
		};
	}
}

echo (new Outer)->func2()->func3();
');

Assert::same(array('Outer'), $phpdepend->getClasses());
Assert::same(array('Outer'), $phpdepend->getDependencies());


/////////////////////////
$phpdepend->parse('<?php
function anonymous_class()
{
	return new class {};
}

if (get_class(anonymous_class()) === get_class(anonymous_class())) {
	echo \'same class\';
} else {
	echo \'different class\';
}
');

Assert::same(array(), $phpdepend->getClasses());
Assert::same(array(), $phpdepend->getDependencies());


/////////////////////////
$phpdepend->parse('<?php
echo get_class(new class {});
}
');

Assert::same(array(), $phpdepend->getClasses());
Assert::same(array(), $phpdepend->getDependencies());
