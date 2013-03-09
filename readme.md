PhpDepend
=========

``` php
$phpdepend = new Cz\PhpDepend;
$source = file_get_contents('MyClass.php');

$phpdepend->parse($source);

var_dump($phpdepend->getClasses()); // returns list of defined classes, interfaces & traits
var_dump($phpdepend->getDependencies()); // returns list of required classes, interfaces & traits
```

License: [New BSD License](license.md)
<br>Author: Jan Pecha, http://janpecha.iunas.cz/
