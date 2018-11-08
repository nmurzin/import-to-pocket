<?php
namespace ImportToPocket;

class Reader
{
    private $fileName;

    public function __construct($fileName = 'info.txt')
    {
        $this->fileName = $fileName;
    }

    public function readFile():array
    {
        $opened = fopen('/var/www/'. $this->fileName, 'r');git
        $result = [];
        while(!feof($opened))
        {
			$result = array_merge($result, $this->parseString(fgets($opened)));
        }

        fclose($opened);

		return $result;
    }

	private function parseString(string $string): array {
    	$string_exploded = explode('|', $string);
    	if(!empty($string_exploded)){
			return [trim($string_exploded[0]) => trim($string_exploded[1])];
		}
	}
}