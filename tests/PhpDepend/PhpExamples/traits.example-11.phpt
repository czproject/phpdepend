<?php

declare(strict_types=1);

/**
 * @phpversion >= 5.4
 */

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

// Example #11 Defining Properties
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse('<?php
trait PropertiesTrait {
    public $x = 1;
}

class PropertiesExample {
    use PropertiesTrait;
}

$example = new PropertiesExample;
$example->x;
');

Assert::same([
	'PropertiesTrait',
	'PropertiesExample',
], $phpdepend->getClasses());
Assert::same([
	'PropertiesTrait',
	'PropertiesExample',
], $phpdepend->getDependencies());
