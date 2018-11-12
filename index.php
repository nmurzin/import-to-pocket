<?php
require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$readerResult = (new \ImportToPocket\Reader())->readFile();
$auth = new \ImportToPocket\Auth();

if ((new \ImportToPocket\Console())->exec($auth->authorize())) {
    new \ImportToPocket\Importer($readerResult, $auth->authenticate());
}

