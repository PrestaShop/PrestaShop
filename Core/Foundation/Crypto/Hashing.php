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
 *  @author     PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Foundation\Crypto;

class Hashing
{
    /** @var array should contain additional hashing methods */
    private $hash_methods = [];

    /**
     * Init $hash_methods
     * @return void
     */
    private function initHashMethods()
    {
        $this->hash_methods = [
                'md5' => [
                    'options' => ['should_rehash' => TRUE],
                    'hash' => function ($passwd, $options) {
                        $cookie_key = (isset($options['cookie_key']) ? $options['cookie_key'] : '');
                        return md5($cookie_key.$passwd);
                    },
                    'verify' => function ($passwd, $hash, $options) {
                        $cookie_key = (isset($options['cookie_key']) ? $options['cookie_key'] : '');
                        /* FIXME: This should be a constant time string check */
                        return md5($cookie_key.$passwd) === $hash;
                    },
                ],
            ];
    }

    /**
     * Check a password against a given hash.
     *
     * @param  string  $passwd        the password you want to check
     * @param  string  $hash          the hash to check against. Note that this is passed by reference,
     *                                so the hash can be transparently updated if needed.
     * @param  string  $options       some additional options :
     *                 - 'cookie_key' the _COOKIE_KEY_ define, used for backward compatibility
     *                                with the deprecated md5 hashing method.
     *
     * @return bool/string            true if the password matches the hash,
     *                                a string if the hash has been updated,
     *                                false otherwise.
     */
    public function checkHash($passwd, $hash, $options = array())
    {
        $should_rehash = false;
        $success = password_verify($passwd, $hash);
        if (!$success) {
            // This hash doesn't come from password_hash, check our own hashing methods
            if (!count($this->hash_methods)) {
                $this->initHashMethods();
            }

            foreach ($this->hash_methods as $name => $method) {
                $success = $method['verify']($passwd, $hash, $options);
                if ($success) {
                    $should_rehash = isset($method['options']['should_rehash']) && $method['options']['should_rehash'];
                    break;
                }
            }
        } else {
            $should_rehash = password_needs_rehash($hash, PASSWORD_DEFAULT, $options);
        }

        // Upgrade the hash only if it's correct, and needs to be upgraded
        if ($success && $should_rehash) {
            $success = $this->hash($passwd);
        }

        return $success;
    }

    /**
     * Returns the hash of the given password.
     *
     * @param  string  $passwd     the password you want to hash.
     *
     * @return string              the hashed password.
     */
    public function hash($passwd)
    {
        return password_hash($passwd, PASSWORD_DEFAULT);
    }

    /**
     * Registers a new hash method so we can support other kinds of hashes.
     *
     * @param string    The hash name.
     * @param callback  A callback to use for checking a hash.
     *                  Expected signature is $callback($passwd, $hash, $options)
     * @param array     Options to use for the hash method.
     *
     */
    public function registerHashMethod($name, $verifyCallback, $options = array()) {
        if (!count($this->hash_methods)) {
            $this->initHashMethods();
        }

        if (isset($this->hash_methods[$name])) {
            // We already have a registered method with that name
            return;
        }

        if (!is_callable($verifyCallback)) {
            throw new Core_Foundation_Exception_Exception('$verifyCallback must be callable');
        }

        $method = array();
        $method['verify'] = $verifyCallback;
        $method['options'] = $options;

        $this->hash_methods[$name] = $method;
    }
}
