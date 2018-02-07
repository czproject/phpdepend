PhpDepend
=========

[![Build Status](https://travis-ci.org/czproject/phpdepend.svg?branch=master)](https://travis-ci.org/czproject/phpdepend)

Extracts list of dependencies (classes, interfaces & traits) from PHP file or code snippet.

<a href="https://www.patreon.com/bePatron?u=9680759"><img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" alt="Become a Patron!" height="35"></a>


Installation
------------

[Download a latest package](https://github.com/czproject/phpdepend/releases) or use [Composer](http://getcomposer.org/):

```
composer require czproject/phpdepend
```

PhpDepend requires PHP 5.3 or later and enabled [Tokenizer extension](http://www.php.net/manual/en/book.tokenizer.php) (enabled by default from PHP 4.3.0).


Usage
-----

``` php
$phpdepend = new Cz\PhpDepend;

// file parsing
$phpdepend->parseFile('MyClass.php');

// code snippet parsing
$source = file_get_contents('MyClass.php');
$phpdepend->parse($source);

// getting result
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
* `self::` - `self` means "this class" → useless (no dependency, class is defined in same file)
* `parent::` - parent class is specified in `extends`
* `static::` - `static` is dynamic-`self` → means "this class", parent or descendant (if exists)

Recognized defined classes (output of `$phpdepend->getClasses()`):
* defined classes (`class MyClass`)
* defined interfaces (`interface MyInterface`)
* defined traits (`trait MyTrait`)


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


------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
