<?php
require __DIR__.'/../Alphapup/Core/ClassLoader/UniversalClassLoader.php';
$loader = new Alphapup\Core\ClassLoader\UniversalClassLoader();
$loader->registerNamespaces(array(
	'Alphapup' => __DIR__.'/..',
	'Application' => __DIR__.'/..',
	'LoremIpsum' => __DIR__.'/../Application',
));
spl_autoload_register(array($loader,'loadClass'));