<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductPricesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use Product;

/**
 * Updates product price information using legacy object models
 */
final class UpdateProductPricesHandler extends AbstractProductHandler implements UpdateProductPricesHandlerInterface
{
    //@todo: FIELD_PRICE OR PROPERTY_PRICE ? are those consts necessary at all?
    /** @var string product price property name */
    private const FIELD_PRICE = 'price';

    /** @var string product ecotax property name */
    private const FIELD_ECOTAX = 'ecotax';

    /** @var string product wholesale_price property name */
    private const FIELD_WHOLESALE_PRICE = 'wholesale_price';

    /** @var string product unit_price property name */
    private const FIELD_UNIT_PRICE = 'unit_price';

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductPricesCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());

        if (null !== $command->getPrice()) {
            $product->price = (float) (string) $command->getPrice();
            $this->validateField($product, self::FIELD_PRICE);
        }

        if (null !== $command->getEcotax()) {
            $product->ecotax = (float) (string) $command->getEcotax();
            $this->validateField($product, self::FIELD_ECOTAX);
        }

        //@todo: validate unsigned int/null?
        //  originally we would validate it one step before (in command when creating TaxRuleGroupId obj),
        //  but that object cannot be created because it is valid value to have 0 as a selection.
        if (null !== $command->getTaxRulesGroupId()) {
            $product->id_tax_rules_group = $command->getTaxRulesGroupId();
        }

        if (null !== $command->isOnSale()) {
            $product->on_sale = $command->isOnSale();
        }

        if (null !== $command->getWholesalePrice()) {
            $product->wholesale_price = (float) (string) $command->getWholesalePrice();
            $this->validateField($product, self::FIELD_WHOLESALE_PRICE);
        }

        //@todo: join unit and unity for better design? because:
        //    it depends from domain rules, it might be that unity must be set always together with unit price
        if (null !== $command->getUnitPrice()) {
            $product->unit_price = (float) (string) $command->getUnitPrice();
            $this->validateField($product, self::FIELD_UNIT_PRICE);
        }

        if (null !== $command->getUnity()) {
            //@todo: unity default value is null. How to identify if its been resetted? use empty string?
            $product->unity = $command->getUnity();
        }

        //@todo: wrap try-catch
        $product->update();
    }

    /**
     * @param Product $product
     * @param string $fieldName
     *
     * @throws ProductConstraintException
     * @throws \PrestaShopException
     */
    private function validateField(Product $product, string $fieldName): void
    {
        $value = $product->{$fieldName};

        if (true !== $product->validateField($fieldName, $value)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid product #%s %s field. Got value "%s"',
                    $product->id,
                    $fieldName,
                    $value
                ),
                $this->getErrorCodeForField($fieldName)
            );
        }
    }

    /**
     * Provides constraint error code for supplied field name.
     *
     * @param string $field
     *
     * @return int
     */
    private function getErrorCodeForField(string $field): int
    {
        $codesByField = [
            self::FIELD_PRICE => ProductConstraintException::INVALID_PRICE,
            self::FIELD_ECOTAX => ProductConstraintException::INVALID_ECOTAX,
            self::FIELD_UNIT_PRICE => ProductConstraintException::INVALID_UNIT_PRICE,
            self::FIELD_WHOLESALE_PRICE => ProductConstraintException::INVALID_WHOLESALE_PRICE,
        ];

        return $codesByField[$field];
    }
}
