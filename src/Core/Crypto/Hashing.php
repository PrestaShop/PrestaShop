<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Crypto;

/**
 * Class Hashing to manage hash and crypto of user (clients/merchants) passwords.
 */
class Hashing
{
    /** @var array should contain hashing methods */
    private $hashMethods = [];

    /**
     * Check if it's the first function of the array that was used for hashing.
     *
     * @param string $passwd The password you want to check
     * @param string $hash The hash you want to check
     * @param string $staticSalt A static salt
     *
     * @return bool Result of the verify function
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
     * Iterate on hash_methods array and return true if it matches.
     *
     * @param string $passwd The password you want to check
     * @param string $hash The hash you want to check
     * @param string $staticSalt A static salt
     *
     * @return bool `true` is returned if the function find a match else false
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
     * contained in PrestaShop\PrestaShop\Core\Crypto\Hashing::hash_methods.
     *
     * @param string $plaintextPassword The password you want to hash
     * @param string $staticSalt The static salt
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
     * Init $hash_methods.
     */
    private function initHashMethods()
    {
        $this->hashMethods = [
            'bcrypt' => [
                'option' => [],
                'hash' => function ($passwd, $staticSalt, $option) {
                    /* @phpstan-ignore-next-line */
                    return password_hash($passwd, PASSWORD_BCRYPT);
                },
                'verify' => function ($passwd, $hash, $staticSalt) {
                    /*
                     * Prevent enumeration because nothing happens
                     * when there is no, or an invalid hash.
                     * Also, change the password to be sure it's not maching
                     * the new hash.
                     * The new hash is equal to 'test' in BCRYPT context.
                     */
                    if (empty($hash)) {
                        $hash = '$2y$10$azRqq.pN0OlWjeVfVMZXOOwqYAx1hMfme6ZnDV.27grGOEZvG.uAO';
                        $passwd = 'wrongPassword';
                    }

                    return password_verify($passwd, $hash);
                },
            ],
            'md5' => [
                'option' => [],
                'hash' => function ($passwd, $staticSalt, $option) {
                    return md5($staticSalt . $passwd);
                },
                'verify' => function ($passwd, $hash, $staticSalt) {
                    return md5($staticSalt . $passwd) === $hash;
                },
            ],
        ];
    }
}
