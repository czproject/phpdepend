<?php
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';

$phpdepend = new Cz\PhpDepend;

// Example #4
// http://php.net/manual/en/language.namespaces.importing.php
$phpdepend->parse('<?php
use My\Full\Classname as Another, My\Full\NSname;

$obj = new Another; // instantiates object of class My\Full\Classname
$obj = new \Another; // instantiates object of class Another
$obj = new Another\thing; // instantiates object of class My\Full\Classname\thing
$obj = new \Another\thing; // instantiates object of class Another\thing
');

Assert::same([], $phpdepend->getClasses());
Assert::same([
	'My\Full\Classname',
	'Another',
	'My\Full\Classname\thing',
	'Another\thing'
], $phpdepend->getDependencies());
