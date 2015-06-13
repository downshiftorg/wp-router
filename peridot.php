<?php
$autoloader = include 'vendor/autoload.php';
$autoloader->add('Rad', __DIR__ . '/specs/lib');

return function($eventEmitter) {
    $eventEmitter->on('peridot.start', function (\Peridot\Console\Environment $environment) {
        $environment->getDefinition()->getArgument('path')->setDefault('specs');
    });
};
