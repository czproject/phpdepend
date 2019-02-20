<?php
use Tester\Assert;
require __DIR__ . '/bootstrap.php';

$phpdepend = new Cz\PhpDepend;
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

Assert::same(array(
	'Greeting',
), $phpdepend->getClasses());

Assert::same(array(
	'IGreeting',
	'InvalidArgumentException',
	'Greeting',
), $phpdepend->getDependencies());
