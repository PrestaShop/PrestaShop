<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject;

/**
 * The surface a CSP policy, rule or violation report belongs to. The storefront is scoped per shop;
 * the back office is a single, global surface across the whole installation.
 */
enum CspContext: string
{
    case FRONT = 'front';
    case ADMIN = 'admin';

    /** The storefront varies per shop; the back office does not, so admin rows are stored globally. */
    public function isPerShop(): bool
    {
        return self::FRONT === $this;
    }
}
