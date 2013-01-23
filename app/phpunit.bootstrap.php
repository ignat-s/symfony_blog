<?php

require_once 'bootstrap.php.cache';

echo 'Prepare mongodb fixtures...', PHP_EOL;
system(sprintf('php %s/console doctrine:mongodb:fixtures:load --env test', __DIR__));
