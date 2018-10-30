<?php
namespace ImportToPocket;

class Reader
{
    private $fileName;

    public function __construct($fileName = 'info.txt')
    {
        $this->fileName = $fileName;
    }

    public function readFile()
    {
        $opened = fopen('/var/www/'. $this->fileName, 'r');
        while(! feof($opened))
        {
            echo fgets($opened). "<br />";
        }

        fclose($opened);
    }
}