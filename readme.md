PhpDepend
=========

Extracts list of dependencies (classes, interfaces & traits) from PHP file or code snippet.

Usage
-----

``` php
$phpdepend = new Cz\PhpDepend;
$phpdepend->parseFile('MyClass.php');

/* or use
$source = file_get_contents('MyClass.php');
$phpdepend->parse($source);
*/

// returns list of defined classes, interfaces & traits
var_dump($phpdepend->getClasses());
// returns list of required classes, interfaces & traits
var_dump($phpdepend->getDependencies());
```

Recognized dependencies in PHP code:
* inherited classes (`extends ClassParent`)
* implemented interfaces (`implements Interface1, Interface2`)
* used traits (`class MyClass { use Trait; }`)
* classes of created instances (`new Object()`)
* static classes (`StaticClass::staticMethod()`, `StaticClass::$staticProperty`)

Ignored dependencies:
* `self::` - `self` means "this class" -> useless (no dependency)
* `parent::` - parent class is specified in `extends`
* `static::` - `static` is dynamic-`self` -> means "this class", parent or descendant (if exists)

Recognized defined classes (output of `$phpdepend->getClasses()`):
* defined classes (`class MyClass`)
* defined interfaces (`interface MyInterface`)
* defined traits (`trait MyTrait`)

[API documentation](http://api.iunas.cz/phpdepend/class-Cz.PhpDepend.html)


Installation
------------

[Download a latest package](https://github.com/czproject/phpdepend/releases) or use [Composer](http://getcomposer.org/):

```
composer require czproject/phpdepend
```

PHPDepend requires PHP 5.3.0 or later and enabled [Tokenizer extension](http://www.php.net/manual/en/book.tokenizer.php) (enabled by default from PHP 4.3.0).


------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, http://janpecha.iunas.cz/

