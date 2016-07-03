<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require __DIR__ . '/vendor/autoload.php';

$app = require(__DIR__ . '/app.php');

return ConsoleRunner::createHelperSet($app['orm.em']);