<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Security;

/**
 * Tells whether a file is a PHP archive (phar), whatever its name or extension.
 *
 * Every phar carries the __HALT_COMPILER(); token in its stub, which no regular data file
 * (CSV, spreadsheet, image...) contains. The file is read in chunks, so large files are
 * checked without being loaded in memory.
 */
class PharDetector
{
    private const CHUNK_SIZE = 1048576;

    /**
     * Kept from the previous chunk, so a token split over two chunks is still found
     */
    private const OVERLAP = 64;

    private const TOKEN_PATTERN = '/__halt_compiler\s*\(\s*\)/i';

    public function isPhar(string $filePath): bool
    {
        $handle = @fopen($filePath, 'rb');
        if (false === $handle) {
            return false;
        }

        try {
            $previousTail = '';
            while (!feof($handle)) {
                $chunk = fread($handle, self::CHUNK_SIZE);
                if (false === $chunk || '' === $chunk) {
                    break;
                }

                $buffer = $previousTail . $chunk;
                if (preg_match(self::TOKEN_PATTERN, $buffer)) {
                    return true;
                }
                $previousTail = substr($buffer, -self::OVERLAP);
            }
        } finally {
            fclose($handle);
        }

        return false;
    }
}
