<?php
require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$readerResult = (new \ImportToPocket\Reader())->readFile();

new \ImportToPocket\Importer($readerResult, (new \ImportToPocket\Auth())->authenticate());