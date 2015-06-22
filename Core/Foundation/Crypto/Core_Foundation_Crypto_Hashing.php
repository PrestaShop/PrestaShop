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

class Core_Foundation_Crypto_Hashing
{

    private $hash_methods = [];

    private function initHashMethods()
    {
        $this->hash_methods = [
                'BCryptSHA256' => [
                    'salt' => 32,
                    'crypt' => function ($passwd, $cookie_key) {
                        return password_hash($cookie_key.$passwd, PASSWORD_BCRYPT);
                    },
                    'verify' => function ($passwd, $hash, $cookie_key) {
                        return password_verify($cookie_key.$passwd, $hash);
                    }
                ],
                'md5' => [
                    'salt' => 32,
                    'crypt' => function ($passwd, $cookie_key) {
                        return md5($cookie_key.$passwd);
                    },
                    'verify' => function ($passwd, $hash, $cookie_key) {
                        return md5($cookie_key.$passwd) === $hash;
                    }
                ]
            ];
    }

    public function encrypt($passwd, $cookie_key)
    {
        if (!count($this->hash_methods)) {
            $this->initHashMethods();
        }

        $closure = reset($this->hash_methods);

        return $closure['crypt']($passwd, $cookie_key);
    }
}
