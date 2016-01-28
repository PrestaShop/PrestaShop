<?php
/**
 * 2007-2015 PrestaShop
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Crypto;

class Hashing
{
    /** @var array should contain hashing methods */
    private $hash_methods = [];

    /**
     * Init $hash_methods
     * @return void
     */
    private function initHashMethods()
    {
        $this->hash_methods = [
                'bcrypt' => [
                    'option' => [],
                    'hash' => function ($passwd, $cookie_key, $option) {
                        return password_hash($passwd, PASSWORD_BCRYPT);
                    },
                    'verify' => function ($passwd, $hash, $cookie_key) {
                        return password_verify($passwd, $hash);
                    }
                ],
                'md5' => [
                    'option' => [],
                    'hash' => function ($passwd, $cookie_key, $option) {
                        return md5($cookie_key.$passwd);
                    },
                    'verify' => function ($passwd, $hash, $cookie_key) {
                        return md5($cookie_key.$passwd) === $hash;
                    }
                ]
            ];
    }

    /**
     * check if it's the first function of the array that was used for hashing
     * @param  string  $passwd     the password you want to check
     * @param  string  $hash       the hash you want to check
     * @param  string  $cookie_key the define _COOKIE_KEY_
     * @return bool                result of the verify function
     */
    public function isFirstHash($passwd, $hash, $cookie_key)
    {
        if (!count($this->hash_methods)) {
            $this->initHashMethods();
        }

        $closure = reset($this->hash_methods);

        return $closure['verify']($passwd, $hash, $cookie_key);
    }

    /**
     * Iter on hash_methods array and return true if it match
     * @param  string  $passwd     the password you want to check
     * @param  string  $hash       the hash you want to check
     * @param  string  $cookie_key the define _COOKIE_KEY_
     * @return bool                true is returned if the function find a match else false
     */
    public function checkHash($passwd, $hash, $cookie_key)
    {
        if (!count($this->hash_methods)) {
            $this->initHashMethods();
        }

        foreach ($this->hash_methods as $closure) {
            if ($closure['verify']($passwd, $hash, $cookie_key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * hash the $passwd string and return the result of the 1st hashing method
     * contained in \PrestaShop\PrestaShop\Core\Foundation\Crypto\Hashing::hash_methods
     * @param  string  $passwd     the password you want to hash
     * @param  string  $cookie_key the define _COOKIE_KEY_
     * @return string
     */
    public function encrypt($passwd, $cookie_key)
    {
        if (!count($this->hash_methods)) {
            $this->initHashMethods();
        }

        $closure = reset($this->hash_methods);

        return $closure['hash']($passwd, $cookie_key, $closure['option']);
    }
}
