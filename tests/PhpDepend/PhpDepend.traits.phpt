<?php
/**
 * @phpversion >= 5.4
 */

use Tester\Assert;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/PhpDepend.php';

$phpdepend = new Cz\PhpDepend;



// Basic
$phpdepend->parse('<?php
	trait ezcReflectionReturnInfo {
	}

	class ezcReflectionMethod extends ReflectionMethod {
		use ezcReflectionReturnInfo;
		/* ... */
	}

	class ezcReflectionFunction extends ReflectionFunction {
		use ezcReflectionReturnInfo;
		/* ... */
	}
');

Assert::same(array(
	'ezcReflectionReturnInfo', 'ezcReflectionMethod', 'ezcReflectionFunction'
), $phpdepend->getClasses());
Assert::same(array(
	'ReflectionMethod', 'ezcReflectionReturnInfo', 'ReflectionFunction'
), $phpdepend->getDependencies());



// Multiple
$phpdepend->parse('<?php
	trait Hello {
	}

	trait World {
	}

	class MyHelloWorld {
		use Hello, World;
	}
');

Assert::same(array('Hello', 'World', 'MyHelloWorld'), $phpdepend->getClasses());
Assert::same(array('Hello', 'World'), $phpdepend->getDependencies());



// Block { }
$phpdepend->parse('<?php
	trait HelloWorld {
	}

	class MyClass1 {
		use HelloWorld { sayHello as protected; }
	}

	class MyClass2 {
		use HelloWorld { sayHello as private myPrivateHello; }
	}
');

Assert::same(array('HelloWorld', 'MyClass1', 'MyClass2'), $phpdepend->getClasses());
Assert::same(array('HelloWorld'), $phpdepend->getDependencies());
