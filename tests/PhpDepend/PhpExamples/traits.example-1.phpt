<?php
/**
 * @phpversion >= 5.4
 */
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

// Example #1 Trait example
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse("<?php
trait ezcReflectionReturnInfo {
    function getReturnType() { /*1*/ }
    function getReturnDescription() { /*2*/ }
}

class ezcReflectionMethod extends ReflectionMethod {
    use ezcReflectionReturnInfo;
    /* ... */
}

class ezcReflectionFunction extends ReflectionFunction {
    use ezcReflectionReturnInfo;
    /* ... */
}
");

Assert::same([
	'ezcReflectionReturnInfo',
	'ezcReflectionMethod',
	'ezcReflectionFunction',
], $phpdepend->getClasses());
Assert::same([
	'ReflectionMethod',
	'ezcReflectionReturnInfo',
	'ReflectionFunction',
], $phpdepend->getDependencies());
