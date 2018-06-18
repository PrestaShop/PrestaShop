<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class PhpEncryption engine for openSSL < 0.9.8.
 *
 * @doc http://php.net/manual/fr/function.mcrypt-encrypt.php#refsect1-function.mcrypt-encrypt-examples
 *
 * This class will be deprecated when web hosting providers will update their version of OpenSSL.
 */
class PhpEncryptionLegacyEngineCore extends PhpEncryptionEngine
{
    protected $key;
    protected $iv;
    protected $ivSize;

    /**
     * PhpEncryptionCore constructor.
     *
     * @param string $hexString A string that only contains hexadecimal characters
     *                          Bother upper and lower case are allowed
     */
    public function __construct($hexString)
    {
        $this->key = substr($hexString, 0, 32);
    }

    protected function getIv()
    {
        if ($this->iv === null) {
            $this->iv = substr(sha1(_COOKIE_IV_), 0, $this->getIvSize());
        }

        return $this->iv;
    }

    protected function getIvSize()
    {
        if ($this->ivSize === null) {
            $this->ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        }

        return $this->ivSize;
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
        $cipherText = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $plaintext, MCRYPT_MODE_CBC, $this->getIv());
        $cipherText = $this->getIv().$cipherText;

        /**
         * Just Encrypt-then-mac practice
         */
        $macKey = mhash_keygen_s2k(MHASH_SHA256, $this->key, $this->getIv(), 32);
        $hmac = hash_hmac('sha256', $this->getIv() . MCRYPT_RIJNDAEL_128 . $cipherText, $macKey);
        return $hmac . ':' . base64_encode($cipherText);
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
        $data = explode(':', $cipherText);
        if (count($data) != 2) {
            return false;
        }

        list($hmac, $encrypted) = $data;
        $encrypted = base64_decode($encrypted);
        $macKey = mhash_keygen_s2k(MHASH_SHA256, $this->key, $this->getIv(), 32);
        $newHmac = hash_hmac('sha256', $this->getIv() . MCRYPT_RIJNDAEL_128 . $encrypted, $macKey);
        if ($hmac !== $newHmac) {
            return false;
        }


        $ivDec = substr($encrypted, 0, $this->getIvSize());
        $cipherText = substr($encrypted, $this->getIvSize());

        return rtrim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_128,
                $this->key,
                $cipherText,
                MCRYPT_MODE_CBC,
                $ivDec
            ),
            "\0"
        );
    }
}
