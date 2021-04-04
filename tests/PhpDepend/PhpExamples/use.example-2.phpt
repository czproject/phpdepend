<?php
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

// Example #2
// http://php.net/manual/en/language.namespaces.importing.php
$phpdepend->parse('<?php
use My\Full\Classname as Another, My\Full\NSname;

$obj = new Another; // instantiates object of class My\Full\Classname
NSname\subns\func(); // calls function My\Full\NSname\subns\func
');

Assert::same([], $phpdepend->getClasses());
Assert::same([
	'My\Full\Classname',
], $phpdepend->getDependencies());
