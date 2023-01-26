<?php

namespace PrestaShop\PrestaShop\Core\Security;

use PrestaShop\PrestaShop\Core\Security\OpenSsl\OpenSSLInterface;

class PasswordGenerator
{
    public const PASSWORDGEN_FLAG_NUMERIC = 'NUMERIC';
    public const PASSWORDGEN_FLAG_NO_NUMERIC = 'NO_NUMERIC';
    public const PASSWORDGEN_FLAG_RANDOM = 'RANDOM';
    public const PASSWORDGEN_FLAG_ALPHANUMERIC = 'ALPHANUMERIC';

    /**
     * @var OpenSSLInterface
     */
    private $cryptography;

    public function __construct(OpenSSLInterface $cryptography)
    {
        $this->cryptography = $cryptography;
    }

    /**
     * Random password generator.
     *
     * @param int $length Desired length (optional)
     * @param string $type Output type (NUMERIC, ALPHANUMERIC, NO_NUMERIC, RANDOM)
     *
     * @return string Password
     */
    public function generatePassword(int $length = 8, string $type = self::PASSWORDGEN_FLAG_ALPHANUMERIC): string
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('Invalid length for password');
        }

        switch ($type) {
            case static::PASSWORDGEN_FLAG_NUMERIC:
                $str = '0123456789';

                break;
            case static::PASSWORDGEN_FLAG_NO_NUMERIC:
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

                break;
            case static::PASSWORDGEN_FLAG_RANDOM:
                $num_bytes = (int) ceil($length * 0.75);
                $bytes = $this->cryptography->getBytes($num_bytes);

                return substr(base64_encode($bytes), 0, $length);
            case static::PASSWORDGEN_FLAG_ALPHANUMERIC:
            default:
                $str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

                break;
        }

        $bytes = $this->cryptography->getBytes($length);
        $position = 0;
        $result = '';

        for ($i = 0; $i < $length; ++$i) {
            $position = ($position + ord($bytes[$i])) % strlen($str);
            $result .= $str[$position];
        }

        return $result;
    }
}
