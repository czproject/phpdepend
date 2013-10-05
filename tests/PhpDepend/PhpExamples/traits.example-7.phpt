<?php
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/PhpDepend.php';

$phpdepend = new Cz\PhpDepend;

// Example #7 Traits Composed from Traits
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse('<?php
trait Hello {
    public function sayHello() {
        echo \'Hello \';
    }
}

trait World {
    public function sayWorld() {
        echo \'World!\';
    }
}

trait HelloWorld {
    use Hello, World;
}

class MyHelloWorld {
    use HelloWorld;
}

$o = new MyHelloWorld();
$o->sayHello();
$o->sayWorld();
');

Assert::same(array(
	'Hello',
	'World',
	'HelloWorld',
	'MyHelloWorld',
), $phpdepend->getClasses());
Assert::same(array(
	'Hello',
	'World',
	'HelloWorld',
	'MyHelloWorld',
), $phpdepend->getDependencies());
