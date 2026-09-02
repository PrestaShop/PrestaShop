<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util;

class InternationalizedDomainNameConverter
{
    /**
     * Convert the host part of the email from punycode to utf8 (e.g,. email@xn--e1aybc.xn--p1ai -> email@тест.рф)
     *
     * @param string $email
     *
     * @return string
     */
    public function emailToUtf8(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        if (defined('INTL_IDNA_VARIANT_UTS46')) {
            return $parts[0] . '@' . idn_to_utf8($parts[1], 0, INTL_IDNA_VARIANT_UTS46);
        }

        if (defined('INTL_IDNA_VARIANT_2003')) {
            return $parts[0] . '@' . idn_to_utf8($parts[1], 0, INTL_IDNA_VARIANT_2003);
        }

        return $parts[0] . '@' . idn_to_utf8($parts[1]);
    }
}
