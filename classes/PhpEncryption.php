<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class PhpEncryptionCore
 *
 *
 */
class PhpEncryptionCore
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
        $this->key = \Defuse\Crypto\Key::loadFromAsciiSafeString($hexString);
    }

    /**
     * Encrypt the plaintext
     *
     * @param string $plaintext Plaintext
     *
     * @return string Cipher text
     */
    public function encrypt($plaintext)
    {
        return Defuse\Crypto\Crypto::encrypt($plaintext, $this->key);
    }

    /**
     * Decrypt the cipher text
     *
     * @param string $cipherText Cipher text
     *
     * @return bool|string Plaintext
     *                     `false` if unable to decrypt
     * @throws Exception
     */
    public function decrypt($cipherText)
    {
        try {
            $plaintext = Defuse\Crypto\Crypto::decrypt($cipherText, $this->key);
        } catch (Exception $e) {
            if ($e instanceof \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException) {
                return false;
            }

            throw $e;
        }

        return $plaintext;
    }
}
