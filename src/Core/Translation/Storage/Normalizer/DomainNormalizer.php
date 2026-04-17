<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Normalizer;

use RuntimeException;

/**
 * Normalizes domain names by removing dots
 */
class DomainNormalizer
{
    /**
     * @param string $domain Domain name
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function normalize(string $domain): string
    {
        // remove up to two dots from the domain name
        // (because legacy domain translations CAN have dots in the third part)
        $normalizedDomain = preg_replace('/\./', '', $domain, 2);

        if ($normalizedDomain === null) {
            throw new RuntimeException(sprintf('An error occurred while normalizing domain "%s"', $domain));
        }

        return $normalizedDomain;
    }
}
