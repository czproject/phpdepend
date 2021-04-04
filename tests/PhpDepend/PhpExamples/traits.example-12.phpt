<?php

/**
 * @phpversion >= 5.4
 */

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

// Example #12 Conflict Resolution
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse('<?php
trait PropertiesTrait {
    public $same = true;
    public $different = false;
}

class PropertiesExample {
    use PropertiesTrait;
    public $same = true; // Strict Standards
    public $different = true; // Fatal error
}
');

Assert::same([
	'PropertiesTrait',
	'PropertiesExample',
], $phpdepend->getClasses());
Assert::same([
	'PropertiesTrait',
], $phpdepend->getDependencies());
