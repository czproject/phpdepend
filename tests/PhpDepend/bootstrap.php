<?php
require __DIR__ . '/../../vendor/autoload.php';
Tester\Environment::setup();

if (extension_loaded('xdebug'))
{
	Tester\CodeCoverage\Collector::start(__DIR__ . '/../coverage.dat');
}
