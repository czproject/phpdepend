<?php

declare(strict_types=1);

/**
 * @phpversion >= 5.4
 */

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;



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

Assert::same([
	'ezcReflectionReturnInfo', 'ezcReflectionMethod', 'ezcReflectionFunction'
], $phpdepend->getClasses());
Assert::same([
	'ReflectionMethod', 'ezcReflectionReturnInfo', 'ReflectionFunction'
], $phpdepend->getDependencies());



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

Assert::same(['Hello', 'World', 'MyHelloWorld'], $phpdepend->getClasses());
Assert::same(['Hello', 'World'], $phpdepend->getDependencies());



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

Assert::same(['HelloWorld', 'MyClass1', 'MyClass2'], $phpdepend->getClasses());
Assert::same(['HelloWorld'], $phpdepend->getDependencies());
