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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;

class ProductTypeFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference type to :productType
     *
     * @param string $productReference
     * @param string $productType
     */
    public function updateProductType(string $productReference, string $productType): void
    {
        $productId = (int) $this->getSharedStorage()->get($productReference);

        try {
            $this->getCommandBus()->handle(new UpdateProductTypeCommand($productId, $productType));
        } catch (InvalidProductTypeException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that this action is allowed for :productType product only
     *
     * @param string $productType
     */
    public function assertLastErrorInvalidProductType(string $productType): void
    {
        $errorCode = null;
        switch ($productType) {
            case 'standard':
                $errorCode = InvalidProductTypeException::EXPECTED_STANDARD_TYPE;
                break;
            case 'pack':
                $errorCode = InvalidProductTypeException::EXPECTED_PACK_TYPE;
                break;
            case 'virtual':
                $errorCode = InvalidProductTypeException::EXPECTED_VIRTUAL_TYPE;
                break;
            case 'combinations':
                $errorCode = InvalidProductTypeException::EXPECTED_COMBINATIONS_TYPE;
                break;
            case 'single':
                $errorCode = InvalidProductTypeException::EXPECTED_NO_COMBINATIONS_TYPE;
                break;
        }
        $this->assertLastErrorIs(InvalidProductTypeException::class, $errorCode);
    }

    /**
     * @Then I should get error that the product is already associated to a pack
     */
    public function assertLastErrorForbiddenAssociations(): void
    {
        $this->assertLastErrorIs(InvalidProductTypeException::class, InvalidProductTypeException::EXPECTED_NO_EXISTING_PACK_ASSOCIATIONS);
    }
}
