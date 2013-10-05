<?php
require __DIR__ . '/../../vendor/nette/tester/Tester/bootstrap.php';

if (extension_loaded('xdebug'))
{
	Tester\CodeCoverage\Collector::start(__DIR__ . '/../coverage.dat');
}
