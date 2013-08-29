PhpDepend
=========

Finds dependencies (classes, interfaces & traits) of PHP file (or code snippet).

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

