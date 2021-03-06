<?php
/**
 * Provides console interface
 */
namespace ImportToPocket;

/**
 * Class Console
 * @package ImportToPocket
 */
class Console
{
	/**
	 * @param string $authUrl
	 *
	 * @return bool
	 */
    public function exec(string $authUrl) : bool
    {
        try {
            echo "You need to authorize this app. \nGo to " . $authUrl . "\nand then say yes:\n";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            if (trim($line) != 'yes') {
                throw new \Exception('Bad response!');
            }
            echo "\n";
            echo "Thank you, continuing...\n";
            return true;
        } catch (\Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
    }
}
