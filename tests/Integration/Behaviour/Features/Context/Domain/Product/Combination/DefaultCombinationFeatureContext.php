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
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetDefaultCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Product;

class DefaultCombinationFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I set combination ":combinationReference" as default
     *
     * @param string $combinationReference
     */
    public function setDefaultCombinationForDefaultShop(string $combinationReference): void
    {
        $this->setDefaultCombination($combinationReference, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I set combination ":combinationReference" as default for shop ":shopReference"
     */
    public function setDefaultCombinationForShop(string $combinationReference, string $shopReference): void
    {
        $this->setDefaultCombination(
            $combinationReference,
            ShopConstraint::shop($this->getSharedStorage()->get($shopReference))
        );
    }

    /**
     * @Then product :productReference default combination should be :combinationReference
     *
     * @param string $productReference
     * @param string $combinationReference
     */
    public function assertDefaultCombinationForDefaultShop(string $productReference, string $combinationReference): void
    {
        $this->assertDefaultCombination($productReference, $combinationReference, $this->getDefaultShopId());
    }

    /**
     * @Then product :productReference default combination for shop :shopReference should be :combinationReference
     *
     * @param string $productReference
     * @param string $shopReference
     * @param string $combinationReference
     */
    public function assertDefaultCombinationForShop(
        string $productReference,
        string $shopReference,
        string $combinationReference
    ): void {
        $this->assertDefaultCombination(
            $productReference,
            $combinationReference,
            $this->getSharedStorage()->get($shopReference)
        );
    }

    /**
     * @param string $productReference
     * @param string $combinationReference
     * @param int $shopId
     */
    private function assertDefaultCombination(
        string $productReference,
        string $combinationReference,
        int $shopId
    ) {
        $combinationId = $this->getSharedStorage()->get($combinationReference);

        $this->assertCachedDefaultCombinationId(
            $productReference,
            $combinationId,
            $shopId
        );

        Assert::assertTrue(
            $this->getCombinationForEditing($combinationReference, $shopId)->isDefault(),
            sprintf('Unexpected default combination in CombinationForEditing for "%s"', $combinationReference)
        );
    }

    /**
     * @Given product :productReference should not have a default combination
     * @Given product :productReference does not have a default combination
     *
     * @param string $productReference
     */
    public function assertProductHasNoCachedDefaultCombination(string $productReference): void
    {
        $this->assertCachedDefaultCombinationId($productReference, 0, $this->getDefaultShopId());
    }

    /**
     * @Given product :productReference should not have a default combination for shop ":shopReference"
     *
     * @param string $productReference
     */
    public function assertProductHasNoCachedDefaultCombinationForShop(string $productReference, string $shopReference): void
    {
        $this->assertCachedDefaultCombinationId(
            $productReference,
            0,
            $this->getSharedStorage()->get($shopReference)
        );
    }

    /**
     * @param string $productReference
     * @param int $combinationId
     */
    private function assertCachedDefaultCombinationId(string $productReference, int $combinationId, int $shopId): void
    {
        $product = new Product(
            $this->getSharedStorage()->get($productReference),
            false,
            null,
            $shopId
        );

        Assert::assertEquals(
            $combinationId,
            (int) $product->cache_default_attribute,
            'Unexpected cached product default combination'
        );
    }

    /**
     * @param string $combinationReference
     * @param ShopConstraint $shopConstraint
     */
    private function setDefaultCombination(string $combinationReference, ShopConstraint $shopConstraint): void
    {
        $this->getCommandBus()->handle(new SetDefaultCombinationCommand(
            (int) $this->getSharedStorage()->get($combinationReference),
            $shopConstraint
        ));
    }
}
