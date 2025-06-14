<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;
$phpdepend->parse('
<?php
	class Greeting implements IGreeting
	{
		public function say($name)
		{
			if (!$name) {
				throw new InvalidArgumentException(\'Invalid name\');
			}
			return "Hello $name";
		}
	}

	$greeting = new Greeting;
	$greeting->say(\'John\');
');

Assert::same([
	'Greeting',
], $phpdepend->getClasses());

Assert::same([
	'IGreeting',
	'InvalidArgumentException',
	'Greeting',
], $phpdepend->getDependencies());
