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

use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;

class DuplicateProductFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I duplicate product :productReference to a :newProductReference
     *
     * @param string $productReference
     * @param string $newProductReference
     */
    public function duplicate(string $productReference, string $newProductReference): void
    {
        $newProductId = $this->getCommandBus()->handle(new DuplicateProductCommand(
            $this->getSharedStorage()->get($productReference)
        ));

        $this->getSharedStorage()->set($newProductReference, $newProductId->getValue());
    }

    /**
     * @Then product :newProductReference should have identical customization fields to :oldProductReference
     *
     * @param string $newProductReference
     * @param string $oldProductReference
     */
    public function assertDuplicatedCustomizationFields(string $newProductReference, string $oldProductReference): void
    {
        $oldCustomizationFields = $this->getProductCustomizationFields($oldProductReference);
        $newCustomizationFields = $this->getProductCustomizationFields($newProductReference);

        Assert::assertEquals(
            count($oldCustomizationFields),
            count($newCustomizationFields),
            'Old product customization fields count differs from new duplicated product'
        );

        foreach ($oldCustomizationFields as $key => $oldCustomizationField) {
            Assert::assertEquals(
                $oldCustomizationField->getLocalizedNames(),
                $newCustomizationFields[$key]->getLocalizedNames(),
                'Unexpected localized names of duplicated product customization field'
            );
            Assert::assertEquals(
                $oldCustomizationField->getType(),
                $newCustomizationFields[$key]->getType(),
                'Unexpected type of duplicated product customization field'
            );
            Assert::assertEquals(
                $oldCustomizationField->isAddedByModule(),
                $newCustomizationFields[$key]->isAddedByModule(),
                'Unexpected "addedByModule" property of duplicated product customization field'
            );
            Assert::assertEquals(
                $oldCustomizationField->isRequired(),
                $newCustomizationFields[$key]->isRequired(),
                'Unexpected "required" property of duplicated product customization field'
            );
        }
    }
}
