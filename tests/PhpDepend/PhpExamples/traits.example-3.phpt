<?php
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/PhpDepend.php';

$phpdepend = new Cz\PhpDepend;

// Example #3 Alternate Precedence Order Example
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse('<?php
trait HelloWorld {
    public function sayHello() {
        echo \'Hello World!\';
    }
}

class TheWorldIsNotEnough {
    use HelloWorld;
    public function sayHello() {
        echo \'Hello Universe!\';
    }
}

$o = new TheWorldIsNotEnough();
$o->sayHello();
');

Assert::same(array(
	'HelloWorld',
	'TheWorldIsNotEnough',
), $phpdepend->getClasses());
Assert::same(array(
	'HelloWorld',
	'TheWorldIsNotEnough',
), $phpdepend->getDependencies());
