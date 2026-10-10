<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\Command;

use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspSource;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/** Adds a source to a surface's curated allow-list (the "Add allowed source" action). */
final class AddCspRuleCommand
{
    public function __construct(
        private readonly string $directive,
        private readonly string $source,
        private readonly ShopConstraint $shopConstraint,
        private readonly CspContext $context = CspContext::FRONT,
    ) {
    }

    public function getContext(): CspContext
    {
        return $this->context;
    }

    public function getDirective(): CspDirective
    {
        return CspDirective::fromString($this->directive);
    }

    public function getSource(): CspSource
    {
        return new CspSource($this->source);
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
