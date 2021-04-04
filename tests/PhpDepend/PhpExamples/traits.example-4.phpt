<?php
/**
 * @phpversion >= 5.4
 */
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';

$phpdepend = new Cz\PhpDepend;

// Example #4 Multiple Traits Usage
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse('<?php
trait Hello {
    public function sayHello() {
        echo \'Hello \';
    }
}

trait World {
    public function sayWorld() {
        echo \'World\';
    }
}

class MyHelloWorld {
    use Hello, World;
    public function sayExclamationMark() {
        echo \'!\';
    }
}

$o = new MyHelloWorld();
$o->sayHello();
$o->sayWorld();
$o->sayExclamationMark();
');

Assert::same([
	'Hello',
	'World',
	'MyHelloWorld',
], $phpdepend->getClasses());
Assert::same([
	'Hello',
	'World',
	'MyHelloWorld',
], $phpdepend->getDependencies());
