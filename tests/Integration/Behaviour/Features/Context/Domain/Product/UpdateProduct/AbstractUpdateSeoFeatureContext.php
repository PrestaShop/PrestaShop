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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\UpdateProduct;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectTarget;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;

/**
 * This abstract class was introduced during UpdateProductCommand unification process,
 * and which idea is to remove multiple sub-commands and use single UpdateProductCommand instead.
 * This abstract context allows sharing assertions which and some other common methods for both implementations during the transition.
 *
 * @see UpdateProductCommand
 * @see UpdateProductHandlerInterface
 *
 * @todo: need to check if this abstract class is still needed when UpdateProductCommand is fully finished,
 *        because one of the contexts that uses it will be deleted, therefore leaving this abstract class useless.
 */
abstract class AbstractUpdateSeoFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @param string $productReference
     * @param string $shopReferences
     * @param TableNode $tableNode
     */
    protected function performAssertSeoOptionsForShops(
        string $productReference,
        string $shopReferences,
        TableNode $tableNode
    ): void {
        foreach (explode(',', $shopReferences) as $shopReference) {
            $this->assertSeoOptions(
                $productReference,
                $this->getSharedStorage()->get(trim($shopReference)),
                $tableNode
            );
        }
    }

    /**
     * @param string $productReference
     * @param TableNode $tableNode
     */
    protected function performAssertSeoOptionsForDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $this->assertSeoOptions(
            $productReference,
            null,
            $tableNode
        );
    }

    /**
     * @param string $productReference
     */
    public function performAssertHasNoRedirectTargetId(string $productReference)
    {
        $productForEditing = $this->getProductForEditing($productReference);

        Assert::assertEquals(
            RedirectTarget::NO_TARGET,
            $productForEditing->getProductSeoOptions()->getRedirectTargetId(),
            'Product "%s" expected to have no redirect target'
        );
        Assert::assertNull($productForEditing->getProductSeoOptions()->getRedirectTarget());
    }

    /**
     * @param string $productReference
     * @param int|null $shopId
     * @param TableNode $tableNode
     */
    private function assertSeoOptions(
        string $productReference,
        ?int $shopId,
        TableNode $tableNode
    ): void {
        $productSeoOptions = $this
            ->getProductForEditing($productReference, $shopId)
            ->getProductSeoOptions()
        ;
        $dataRows = $tableNode->getRowsHash();

        $redirectType = $dataRows['redirect_type'] ?? RedirectType::TYPE_PRODUCT_PERMANENT;
        if (isset($dataRows['redirect_type'])) {
            Assert::assertEquals(
                $dataRows['redirect_type'],
                $productSeoOptions->getRedirectType(),
                'Unexpected redirect_type'
            );
            unset($dataRows['redirect_type']);
        }

        $expectedRedirectTarget = isset($dataRows['redirect_target']) ?
            $this->getSharedStorage()->get($dataRows['redirect_target']) :
            RedirectTarget::NO_TARGET
        ;

        Assert::assertEquals(
            $expectedRedirectTarget,
            $productSeoOptions->getRedirectTargetId(),
            'Unexpected redirect target'
        );
        if ($expectedRedirectTarget) {
            Assert::assertEquals(
                $expectedRedirectTarget,
                $productSeoOptions->getRedirectTarget()->getId(),
                'Unexpected redirect target'
            );
        } else {
            Assert::assertNull(
                $productSeoOptions->getRedirectTarget(),
                'Unexpected redirect target'
            );
        }
        unset($dataRows['redirect_target']);

        if (isset($dataRows['redirect_name'])) {
            Assert::assertEquals(
                $dataRows['redirect_name'],
                $productSeoOptions->getRedirectTarget()->getName(),
                'Unexpected redirect_name'
            );
            unset($dataRows['redirect_name']);
        }

        if (isset($dataRows['redirect_image'])) {
            switch ($redirectType) {
                case RedirectType::TYPE_CATEGORY_TEMPORARY:
                case RedirectType::TYPE_CATEGORY_PERMANENT:
                    $realImageUrl = $this->getRealCategoryImageUrl($dataRows['redirect_image']);
                    break;
                default:
                    $realImageUrl = $this->getRealImageUrl($dataRows['redirect_image']);
                    break;
            }

            Assert::assertEquals(
                $realImageUrl,
                $productSeoOptions->getRedirectTarget()->getImage(),
                'Unexpected redirect_image'
            );
            unset($dataRows['redirect_image']);
        }
    }
}
