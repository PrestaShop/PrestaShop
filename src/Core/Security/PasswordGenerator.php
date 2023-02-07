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
