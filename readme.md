PhpDepend
=========

Extracts list of dependencies (classes, interfaces & traits) from PHP file or code snippet.

Usage
-----

``` php
$phpdepend = new Cz\PhpDepend;
$phpdepend->parseFile('MyClass.php');

/* or use */
$source = file_get_contents('MyClass.php');
$phpdepend->parse($source);

$phpdepend->getClasses(); // returns list of defined classes, interfaces & traits
$phpdepend->getDependencies(); // returns list of required classes, interfaces & traits
```

Recognized dependencies in PHP code:
* inherited classes (`extends ParentClass`)
* implemented interfaces (`implements InterfaceA, InterfaceB`)
* used traits (`class MyClass { use Trait; }`)
* classes of created instances (`new Object()`)
* static classes (`StaticClass::staticMethod()`, `StaticClass::$staticProperty`)

Ignored dependencies:
* `self::` - `self` means "this class" → useless (no dependency, class defined in same file)
* `parent::` - parent class is specified in `extends`
* `static::` - `static` is dynamic-`self` → means "this class", parent or descendant (if exists)

Recognized defined classes (output of `$phpdepend->getClasses()`):
* defined classes (`class MyClass`)
* defined interfaces (`interface MyInterface`)
* defined traits (`trait MyTrait`)

[API documentation](http://api.iunas.cz/phpdepend/class-Cz.PhpDepend.html)


Example
-------

``` php
<?php
$phpdepend = new Cz\PhpDepend;
$phpdepend->parse('
<?php
	class Greeting implements IGreeting
	{
		public function say($name)
		{
			if (!$name) {
				throw new InvalidArgumentException("Invalid name");
			}
			return "Hello $name";
		}
	}

	$greeting = new Greeting;
	$greeting->say("John");
');

var_dump($phpdepend->getClasses());
/* Output:
array (1) {
	'Greeting'
}
*/

var_dump($phpdepend->getDependencies());
/* Output:
array (3) {
	'IGreeting',
	'InvalidArgumentException',
	'Greeting',
}
*/
```


Installation
------------

[Download a latest package](https://github.com/czproject/phpdepend/releases) or use [Composer](http://getcomposer.org/):

```
composer require czproject/phpdepend
```

PhpDepend requires PHP 5.3 or later and enabled [Tokenizer extension](http://www.php.net/manual/en/book.tokenizer.php) (enabled by default from PHP 4.3.0).


------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, http://janpecha.iunas.cz/

[![Build Status](https://travis-ci.org/czproject/phpdepend.svg?branch=master)](https://travis-ci.org/czproject/phpdepend)
