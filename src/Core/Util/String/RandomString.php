<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util\String;

class RandomString
{
    public static function generate(int $length = 32): string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }

    /**
     * Generates a random string from the given set of characters.
     * ex: generateFromCharacters('ABCDEF0123456789', 10) to generate a random hexadecimal string of length 10
     *
     * @param string $characters Characters to use for generating the string
     * @param int $length Length of the generated string
     *
     * @return string Generated random string
     */
    public static function generateFromCharacters(string $characters, int $length): string
    {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
