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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkUpdateProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Transform\StringToBoolTransformContext;

class UpdateStatusFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When /^I bulk change status to be (enabled|disabled) for following products:$/
     *
     * @param bool $status
     * @param TableNode $productsList
     */
    public function bulkUpdateStatus(bool $status, TableNode $productsList): void
    {
        $productIds = [];
        foreach ($productsList->getColumnsHash() as $productInfo) {
            $productIds[] = $this->getSharedStorage()->get($productInfo['reference']);
        }

        try {
            $this->getCommandBus()->handle(new BulkUpdateProductStatusCommand(
                $productIds,
                $status
            ));
        } catch (ProductException $e) {
            $this->setLastException($e);

            return;
        }
    }

    /**
     * @When /^I (enable|disable) product "(.*)"$/
     *
     * status transformation handled by @see StringToBoolTransformContext
     *
     * @param bool $status
     * @param string $productReference
     */
    public function updateStatus(bool $status, string $productReference): void
    {
        try {
            $this->getCommandBus()->handle(new UpdateProductStatusCommand(
                $this->getSharedStorage()->get($productReference),
                $status
            ));
        } catch (ProductConstraintException $e) {
            if (ProductConstraintException::INVALID_ONLINE_DATA === $e->getCode()) {
                $this->setLastException($e);
            } else {
                throw $e;
            }
        }
    }

    /**
     * @Then /^product "(.*)" should be (enabled|disabled)$/
     *
     * status transformation handled by @see StringToBoolTransformContext
     *
     * @param string $productReference
     * @param bool $expectedStatus
     */
    public function assertStatus(string $productReference, bool $expectedStatus): void
    {
        $actualStatus = $this->extractValueFromProductForEditing($this->getProductForEditing($productReference), 'active');
        Assert::assertSame($expectedStatus, $actualStatus, 'Unexpected product status');
    }

    /**
     * @Then /^product "(.*)" should be (enabled|disabled) for shops "(.*)"$/
     *
     * Status transformation handled by @see StringToBoolTransformContext
     *
     * @param string $productReference
     * @param bool $expectedStatus
     * @param string $shopReferences
     */
    public function assertStatusForShops(string $productReference, bool $expectedStatus, string $shopReferences): void
    {
        $shopReferences = explode(',', $shopReferences);
        foreach ($shopReferences as $shopReference) {
            $shopId = $this->getSharedStorage()->get($shopReference);
            $actualStatus = $this->extractValueFromProductForEditing($this->getProductForEditing($productReference, $shopId), 'active');
            Assert::assertSame($expectedStatus, $actualStatus, sprintf('Unexpected product status for shop %s', $shopReference));
        }
    }

    /**
     * @Then I should get an error that product online data are invalid
     */
    public function assertInvalidOnlineDataException(): void
    {
        $this->assertLastErrorIs(ProductConstraintException::class, ProductConstraintException::INVALID_ONLINE_DATA);
    }

    /**
     * @Then I should get an error that online data are invalid for products:
     *
     * @param TableNode $productsList
     */
    public function assertExceptionRoseForProducts(TableNode $productsList): void
    {
        /** @var CannotBulkUpdateProductException $bulkException */
        $bulkException = $this->assertLastErrorIs(CannotBulkUpdateProductException::class);

        $invalidProductIds = [];
        foreach ($productsList->getColumnsHash() as $productInfo) {
            $invalidProductIds[] = $this->getSharedStorage()->get($productInfo['reference']);
        }

        foreach ($bulkException->getBulkExceptions() as $productId => $productException) {
            Assert::assertContains($productId, $invalidProductIds);
            if (!$productException instanceof ProductConstraintException) {
                throw new RuntimeException(sprintf(
                    'Product error should be "%s", but got "%s"',
                    ProductConstraintException::class,
                    get_class($productException)
                ));
            }

            if ($productException->getCode() !== ProductConstraintException::INVALID_ONLINE_DATA) {
                throw new RuntimeException(sprintf(
                    'Last error should have code "%s", but has "%s"',
                    ProductConstraintException::INVALID_ONLINE_DATA,
                    $productException->getCode()
                ));
            }
        }
    }
}
