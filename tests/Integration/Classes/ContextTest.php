<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Context;
use PHPUnit\Framework\TestCase;
use Shop;

class ContextTest extends TestCase
{
    /**
     * Context::getShopConstraint() must expose the legacy multistore selection
     * (Shop::getContext()): all shops and shop group in the back office, the context
     * shop otherwise — so consumers like the extra property services follow the
     * multistore header instead of always targeting one shop.
     */
    public function testGetShopConstraintExposesTheMultistoreSelection(): void
    {
        $previousContext = Shop::getContext();
        $previousGroupId = Shop::getContextShopGroupID();

        try {
            Shop::setContext(Shop::CONTEXT_ALL);
            $this->assertTrue(Context::getContext()->getShopConstraint()->forAllShops());

            $defaultGroupId = (int) Shop::getGroupFromShop(1, true);
            Shop::setContext(Shop::CONTEXT_GROUP, $defaultGroupId);
            $constraint = Context::getContext()->getShopConstraint();
            $this->assertSame($defaultGroupId, $constraint->getShopGroupId()->getValue());
            $this->assertNull($constraint->getShopId());

            Shop::setContext(Shop::CONTEXT_SHOP, 1);
            $constraint = Context::getContext()->getShopConstraint();
            $this->assertSame((int) Context::getContext()->shop->id, $constraint->getShopId()->getValue());

            // Shop::setContext(CONTEXT_GROUP, null) is legal and leaves the group id at 0:
            // the getter must stay total and fall back to the single-shop constraint
            // instead of throwing (ShopGroupId rejects 0).
            Shop::setContext(Shop::CONTEXT_GROUP, null);
            $constraint = Context::getContext()->getShopConstraint();
            $this->assertNull($constraint->getShopGroupId());
            $this->assertSame((int) Context::getContext()->shop->id, $constraint->getShopId()->getValue());
        } finally {
            Shop::setContext($previousContext ?? Shop::CONTEXT_ALL, $previousGroupId ?: null);
        }
    }
}
