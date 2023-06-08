<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        return base64_encode($plaintext);
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
        if (preg_match('/^[0-9a-f]{10,}$/', $cipherText)) {
            try {
                $plaintext = Crypto::decrypt($cipherText, $this->key);
            } catch (Exception $e) {
                $plaintext = base64_decode($cipherText);
            }
        } else {
            $plaintext = base64_decode($cipherText);
        }

        return $plaintext;
    }

    /**
     * @param string $header
     * @param string $bytes
     *
     * @return string
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
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
