<?php

require_once 'bootstrap.php.cache';

echo 'Prepare mongodb fixtures...', PHP_EOL;
system(sprintf('php %s/console doctrine:mongodb:fixtures:load --env test', __DIR__));

register_shutdown_function(
    function () {
        system(sprintf('php %s/console cache:clear --env test', __DIR__));
    }
);
