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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Accessor\CommandAccessor;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Accessor\CommandAccessorConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Accessor\CommandField;

/**
 * Builder used to build UpdateProductPricesCommand
 */
class PricesCommandsBuilder implements MultistoreProductCommandsBuilderInterface
{
    /**
     * @var string
     */
    private $modifyAllNamePrefix;

    /**
     * @param string $modifyAllNamePrefix
     */
    public function __construct(string $modifyAllNamePrefix)
    {
        $this->modifyAllNamePrefix = $modifyAllNamePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopId $shopId): array
    {
        if (!isset($formData['pricing'])) {
            return [];
        }

        $priceData = $formData['pricing'];
        $config = new CommandAccessorConfig($this->modifyAllNamePrefix);
        $config
            ->addMultiStoreField('[retail_price][price_tax_excluded]', 'setPrice', CommandField::TYPE_STRING)
            ->addMultiStoreField('[retail_price][ecotax]', 'setEcotax', CommandField::TYPE_STRING)
            ->addMultiStoreField('[tax_rules_group_id]', 'setTaxRulesGroupId', CommandField::TYPE_STRING)
            ->addMultiStoreField('[on_sale]', 'setOnSale', CommandField::TYPE_STRING)
            ->addMultiStoreField('[wholesale_price]', 'setWholesalePrice', CommandField::TYPE_STRING)
            ->addMultiStoreField('[unit_price][price_tax_excluded]', 'setUnitPrice', CommandField::TYPE_STRING)
            ->addMultiStoreField('[unit_price][unity]', 'setUnity', CommandField::TYPE_STRING)
        ;

        $commandAccessor = new CommandAccessor($config);
        $shopCommand = new UpdateProductPricesCommand($productId->getValue(), ProductShopConstraint::shop($shopId->getValue()));
        $allShopsCommand = new UpdateProductPricesCommand($productId->getValue(), ProductShopConstraint::allShops());

        return $commandAccessor->buildCommands($priceData, $shopCommand, $allShopsCommand);
    }
}
