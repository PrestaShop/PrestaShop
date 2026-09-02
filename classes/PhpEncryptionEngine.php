<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Encoding;
use Defuse\Crypto\Key;

/**
 * Class PhpEncryption engine for openSSL 1.0.1+.
 */
class PhpEncryptionEngineCore
{
    protected $key;

    /**
     * PhpEncryptionCore constructor.
     *
     * @param string $hexString A string that only contains hexadecimal characters
     *                          Bother upper and lower case are allowed
     */
    public function __construct($hexString)
    {
        $this->key = self::loadFromAsciiSafeString($hexString);
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
        return Crypto::encrypt($plaintext, $this->key);
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
        try {
            $plaintext = Crypto::decrypt($cipherText, $this->key);
        } catch (Exception $e) {
            if ($e instanceof Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException) {
                return false;
            }

            throw $e;
        }

        return $plaintext;
    }

    /**
     * @param string $header
     * @param string $bytes
     *
     * @return string
     *
     * @throws Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function saveBytesToChecksummedAsciiSafeString($header, $bytes)
    {
        return Encoding::saveBytesToChecksummedAsciiSafeString($header, $bytes);
    }

    /**
     * @return string
     */
    public static function createNewRandomKey()
    {
        $key = Key::createNewRandomKey();

        return $key->saveToAsciiSafeString();
    }

    /**
     * @param string $hexString
     *
     * @return Key
     */
    public static function loadFromAsciiSafeString($hexString)
    {
        return Key::loadFromAsciiSafeString($hexString);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public static function randomCompat()
    {
        $bytes = Key::KEY_BYTE_SIZE;

        $secure = true;
        $buf = openssl_random_pseudo_bytes($bytes, $secure);
        if (
            $buf !== false
            && $secure
            && mb_strlen($buf, '8bit') === $bytes
        ) {
            return $buf;
        }

        throw new Exception('Could not gather sufficient random data');
    }

    /**
     * @param string $buf
     *
     * @return string
     */
    public static function saveToAsciiSafeString($buf)
    {
        return Encoding::saveBytesToChecksummedAsciiSafeString(
            Key::KEY_CURRENT_VERSION,
            $buf
        );
    }
}
