<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\Command;

use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Clears the collected CSP violation log for the given shop scope and surface (front or back office).
 */
final class ClearCspLogCommand
{
    public function __construct(
        private readonly ShopConstraint $shopConstraint,
        private readonly CspContext $context = CspContext::FRONT,
    ) {
    }

    public function getContext(): CspContext
    {
        return $this->context;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
