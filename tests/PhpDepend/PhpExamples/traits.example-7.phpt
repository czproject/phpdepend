<?php
/**
 * @phpversion >= 5.4
 */
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

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

Assert::same([
	'Hello',
	'World',
	'HelloWorld',
	'MyHelloWorld',
], $phpdepend->getClasses());
Assert::same([
	'Hello',
	'World',
	'HelloWorld',
	'MyHelloWorld',
], $phpdepend->getDependencies());
