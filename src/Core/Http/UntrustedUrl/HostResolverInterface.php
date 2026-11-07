<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Http\UntrustedUrl;

interface HostResolverInterface
{
    /**
     * @return string[] every IPv4 and IPv6 address the host resolves to, empty when it does not resolve
     */
    public function resolve(string $host): array;
}
