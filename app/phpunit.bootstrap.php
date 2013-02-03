<?php

require_once 'bootstrap.php.cache';

//$doctrineType = isset($_SERVER['DOCTRINE_TYPE']) ? $_SERVER['DOCTRINE_TYPE'] : 'orm';
$doctrineType = 'mongodb';
$fixturesPath = __DIR__ . '/../src/Acme/BlogBundle/DataFixtures';

if ($doctrineType == 'orm') {
    $fixturesCommand = sprintf('doctrine:fixtures:load --fixtures %s', $fixturesPath);
} elseif ($doctrineType == 'mongodb') {
    $fixturesCommand = sprintf('doctrine:mongodb:fixtures:load --fixtures %s', $fixturesPath);
} else {
    throw new \Exception('Invalid doctrine type: ' . $doctrineType);
}

echo 'Loading fixtures...', PHP_EOL;

system(sprintf('php %s/console %s --env test', __DIR__, $fixturesCommand));
