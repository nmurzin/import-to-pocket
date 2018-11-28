<?php
/**
 * Read file
 */
namespace ImportToPocket;

/**
 * Class Reader
 * @package ImportToPocket
 */
class Reader
{
	/** @var string **/
    private $fileName;

	/**
	 * Reader constructor.
	 *
	 * @param string $fileName
	 */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

	/**
	 * @return array
	 */
    public function readFile(): array
    {
        $opened = fopen('/var/www/' . $this->fileName, 'r');
        $result = [];
        while (!feof($opened)) {
            $result = array_merge($result, $this->parseString(fgets($opened)));
        }

        fclose($opened);

        return $result;
    }

	/**
	 * @param string $string
	 *
	 * @return array
	 */
    private function parseString(string $string): array
    {
        $string_exploded = explode('|', $string);
        if (!empty($string_exploded)) {
            return [trim($string_exploded[0]) => trim($string_exploded[1])];
        }
    }
}
