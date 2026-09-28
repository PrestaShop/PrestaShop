<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Csp\CspPolicy;

/**
 * Builds the CSP policy sent on the storefront for a shop.
 *
 * In this phase it returns the base collection policy only. It is deliberately tight so that,
 * under report-only, the browser reports the shop's full external and inline footprint for the
 * merchant to curate. Only data: images and fonts are pre-allowed, because they are ubiquitous
 * and low-risk and would otherwise flood the log. Curated rules, theme contributions and module
 * hook contributions are merged in here in a later phase.
 */
final class CspPolicyProvider
{
    public function getPolicy(int $shopId): CspPolicy
    {
        $policy = new CspPolicy();

        $policy->addSource('default-src', "'self'");
        $policy->addSource('img-src', "'self'");
        $policy->addSource('img-src', 'data:');
        $policy->addSource('font-src', "'self'");
        $policy->addSource('font-src', 'data:');

        return $policy;
    }
}
