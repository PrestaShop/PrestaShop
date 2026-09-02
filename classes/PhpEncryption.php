<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;

/**
 * Class PhpEncryptionCore for openSSL 1.0.1+.
 */
class PhpEncryptionCore
{
    public const ENGINE = 'PhpEncryptionEngine';

    private static $engine;

    /**
     * PhpEncryptionCore constructor.
     *
     * @param string $hexString A string that only contains hexadecimal characters
     *                          Bother upper and lower case are allowed
     */
    public function __construct($hexString)
    {
        $engineClass = self::resolveEngineToUse();
        self::$engine = new $engineClass($hexString);
    }

    /**
     * Encrypt the plaintext.
     *
     * @param string $plaintext Plaintext
     *
     * @return string Cipher text
     */
    public function encrypt($plaintext)
    {
        return self::$engine->encrypt($plaintext);
    }

    /**
     * Decrypt the cipher text.
     *
     * @param string $cipherText Cipher text
     *
     * @return bool|string Plaintext
     *                     `false` if unable to decrypt
     *
     * @throws Exception
     */
    public function decrypt($cipherText)
    {
        return self::$engine->decrypt($cipherText);
    }

    /**
     * @param string $header
     * @param string $bytes
     *
     * @return string
     *
     * @throws EnvironmentIsBrokenException
     */
    public static function saveBytesToChecksummedAsciiSafeString($header, $bytes)
    {
        $engine = self::resolveEngineToUse();

        return $engine::saveBytesToChecksummedAsciiSafeString($header, $bytes);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public static function createNewRandomKey()
    {
        $engine = self::resolveEngineToUse();

        try {
            $randomKey = $engine::createNewRandomKey();
        } catch (EnvironmentIsBrokenException $exception) {
            $buf = $engine::randomCompat();
            $randomKey = $engine::saveToAsciiSafeString($buf);
        }

        return $randomKey;
    }

    /**
     * Choose which engine use regarding the OpenSSL cipher methods available.
     */
    public static function resolveEngineToUse()
    {
        return self::ENGINE;
    }
}
