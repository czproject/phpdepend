<?php
/**
 * @phpversion >= 5.4
 */
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';

$phpdepend = new CzProject\PhpDepend\PhpDepend;

// Example #8 Express Requirements by Abstract Methods
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse('<?php
trait Hello {
    public function sayHelloWorld() {
        echo \'Hello\'.$this->getWorld();
    }
    abstract public function getWorld();
}

class MyHelloWorld {
    private $world;
    use Hello;
    public function getWorld() {
        return $this->world;
    }
    public function setWorld($val) {
        $this->world = $val;
    }
}
');

Assert::same([
	'Hello',
	'MyHelloWorld',
], $phpdepend->getClasses());
Assert::same([
	'Hello',
], $phpdepend->getDependencies());
