<?php
require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$readerResult = (new \ImportToPocket\Reader(getenv('SOURCE')))->readFile();
$auth = new \ImportToPocket\Auth();

if ((new \ImportToPocket\Console())->exec($auth->authorize())) {
    new \ImportToPocket\Importer($readerResult, $auth->authenticate(), getenv('POCKET_CONSUMER_KEY'));
}

