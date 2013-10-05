<?php
use Tester\Assert;
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/PhpDepend.php';

$phpdepend = new Cz\PhpDepend;

// Example #5 Conflict Resolution
// http://www.php.net/manual/en/language.oop5.traits.php
$phpdepend->parse("<?php
trait A {
    public function smallTalk() {
        echo 'a';
    }
    public function bigTalk() {
        echo 'A';
    }
}

trait B {
    public function smallTalk() {
        echo 'b';
    }
    public function bigTalk() {
        echo 'B';
    }
}

class Talker {
    use A, B {
        B::smallTalk insteadof A;
        A::bigTalk insteadof B;
    }
}

class Aliased_Talker {
    use A, B {
        B::smallTalk insteadof A;
        A::bigTalk insteadof B;
        B::bigTalk as talk;
    }
}
");

Assert::same(array(
	'A',
	'B',
	'Talker',
	'Aliased_Talker',
), $phpdepend->getClasses());
Assert::same(array(
	'A',
	'B',
), $phpdepend->getDependencies());
