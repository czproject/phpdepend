<?php
/**
 * @phpversion >= 5.4
 */
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

// Example #10 Static Methods
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse('<?php
trait StaticExample {
    public static function doSomething() {
        return \'Doing something\';
    }
}

class Example {
    use StaticExample;
}

Example::doSomething();
');

Assert::same([
	'StaticExample',
	'Example',
], $phpdepend->getClasses());
Assert::same([
	'StaticExample',
	'Example',
], $phpdepend->getDependencies());
