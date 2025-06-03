<?php

declare(strict_types=1);

/**
 * @phpversion >= 5.4
 */

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

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

Assert::same([
	'HelloWorld',
	'TheWorldIsNotEnough',
], $phpdepend->getClasses());
Assert::same([
	'HelloWorld',
	'TheWorldIsNotEnough',
], $phpdepend->getDependencies());
