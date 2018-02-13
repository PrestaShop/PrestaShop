<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Crypto;

/**
 * Class Hashing to manage hash and crypto of user (clients/merchants) passwords.
 *
 * @package PrestaShop\PrestaShop\Core\Crypto
 */
class Hashing
{
    /** @var array should contain hashing methods */
    private $hashMethods = array();

    /**
     * Check if it's the first function of the array that was used for hashing
     *
     * @param  string $passwd     The password you want to check
     * @param  string $hash       The hash you want to check
     * @param  string $staticSalt A static salt
     *
     * @return bool              Result of the verify function
     */
    public function isFirstHash($passwd, $hash, $staticSalt = _COOKIE_KEY_)
    {
        if (!count($this->hashMethods)) {
            $this->initHashMethods();
        }

        $closure = reset($this->hashMethods);

        return $closure['verify']($passwd, $hash, $staticSalt);
    }

    /**
     * Iterate on hash_methods array and return true if it matches
     *
     * @param  string $passwd     The password you want to check
     * @param  string $hash       The hash you want to check
     * @param  string $staticSalt A static salt
     *
     * @return bool               `true` is returned if the function find a match else false
     */
    public function checkHash($passwd, $hash, $staticSalt = _COOKIE_KEY_)
    {
        if (!count($this->hashMethods)) {
            $this->initHashMethods();
        }

        foreach ($this->hashMethods as $closure) {
            if ($closure['verify']($passwd, $hash, $staticSalt)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hash the `$plaintextPassword` string and return the result of the 1st hashing method
     * contained in PrestaShop\PrestaShop\Core\Crypto\Hashing::hash_methods
     *
     * @param string $plaintextPassword The password you want to hash
     * @param string $staticSalt        The static salt
     *
     * @return string
     */
    public function hash($plaintextPassword, $staticSalt = _COOKIE_KEY_)
    {
        if (!count($this->hashMethods)) {
            $this->initHashMethods();
        }

        $closure = reset($this->hashMethods);

        return $closure['hash']($plaintextPassword, $staticSalt, $closure['option']);
    }

    /**
     * Init $hash_methods
     *
     * @return void
     */
    private function initHashMethods()
    {
        $this->hashMethods = array(
            'bcrypt' => array(
                'option' => array(),
                'hash' => function ($passwd, $staticSalt, $option) {
                    return password_hash($passwd, PASSWORD_BCRYPT);
                },
                'verify' => function ($passwd, $hash, $staticSalt) {
                    return password_verify($passwd, $hash);
                },
            ),
            'md5' => array(
                'option' => array(),
                'hash' => function ($passwd, $staticSalt, $option) {
                    return md5($staticSalt.$passwd);
                },
                'verify' => function ($passwd, $hash, $staticSalt) {
                    return md5($staticSalt.$passwd) === $hash;
                },
            ),
        );
    }
}
