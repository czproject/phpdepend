<?php
/**
 * @phpversion >= 5.4
 */
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';

$phpdepend = new Cz\PhpDepend;

// Example #2 Precedence Order Example
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse('<?php
class Base {
    public function sayHello() {
        echo \'Hello \';
    }
}

trait SayWorld {
    public function sayHello() {
        parent::sayHello();
        echo \'World!\';
    }
}

class MyHelloWorld extends Base {
    use SayWorld;
}

$oh = new MyHelloWorld();
$oh->sayHello();
');

Assert::same(array(
	'Base',
	'SayWorld',
	'MyHelloWorld',
), $phpdepend->getClasses());
Assert::same(array(
	'Base',
	'SayWorld',
	'MyHelloWorld',
), $phpdepend->getDependencies());
