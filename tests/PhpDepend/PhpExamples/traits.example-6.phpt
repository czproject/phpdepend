<?php
/**
 * @phpversion >= 5.4
 */
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/PhpDepend.php';

$phpdepend = new Cz\PhpDepend;

// Example #6 Changing Method Visibility
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse("<?php
trait HelloWorld {
    public function sayHello() {
        echo 'Hello World!';
    }
}

// Change visibility of sayHello
class MyClass1 {
    use HelloWorld { sayHello as protected; }
}

// Alias method with changed visibility
// sayHello visibility not changed
class MyClass2 {
    use HelloWorld { sayHello as private myPrivateHello; }
}
");

Assert::same(array(
	'HelloWorld',
	'MyClass1',
	'MyClass2',
), $phpdepend->getClasses());
Assert::same(array(
	'HelloWorld',
), $phpdepend->getDependencies());
