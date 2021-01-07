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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductType;

/**
 * Provides the data that is used to prefill the Product form
 */
final class ProductFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        /** @var ProductForEditing $productForEditing */
        $productForEditing = $this->queryBus->handle(new GetProductForEditing((int) $id));

        return [
            'id' => $id,
            'basic' => $this->extractBasicData($productForEditing),
            'price' => $this->extractPriceData($productForEditing),
            'shipping' => $this->extractShippingData($productForEditing),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'basic' => [
                'type' => ProductType::TYPE_STANDARD,
            ],
            'price' => [
                'price_tax_excluded' => 0,
                'price_tax_included' => 0,
                'wholesale_price' => 0,
                'unit_price' => 0,
            ],
            'shipping' => [
                'width' => 0,
                'height' => 0,
                'depth' => 0,
                'weight' => 0,
            ],
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractBasicData(ProductForEditing $productForEditing): array
    {
        return [
            'name' => $productForEditing->getBasicInformation()->getLocalizedNames(),
            'type' => $productForEditing->getBasicInformation()->getType()->getValue(),
            'description' => $productForEditing->getBasicInformation()->getLocalizedDescriptions(),
            'description_short' => $productForEditing->getBasicInformation()->getLocalizedShortDescriptions(),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractPriceData(ProductForEditing $productForEditing): array
    {
        return [
            'price_tax_excluded' => (float) (string) $productForEditing->getPricesInformation()->getPrice(),
            // @todo: we don't have the price tax included for now This should be computed by GetProductForEditing
            'price_tax_included' => (float) (string) $productForEditing->getPricesInformation()->getPrice(),
            'ecotax' => (float) (string) $productForEditing->getPricesInformation()->getEcotax(),
            'tax_rules_group_id' => $productForEditing->getPricesInformation()->getTaxRulesGroupId(),
            'on_sale' => $productForEditing->getPricesInformation()->isOnSale(),
            'wholesale_price' => (float) (string) $productForEditing->getPricesInformation()->getWholesalePrice(),
            'unit_price' => (float) (string) $productForEditing->getPricesInformation()->getUnitPrice(),
            'unity' => $productForEditing->getPricesInformation()->getUnity(),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractShippingData(ProductForEditing $productForEditing): array
    {
        $shipping = $productForEditing->getShippingInformation();

        return [
            'width' => (string) $shipping->getWidth(),
            'height' => (string) $shipping->getHeight(),
            'depth' => (string) $shipping->getDepth(),
            'weight' => (string) $shipping->getWeight(),
            'additional_shipping_cost' => (string) $shipping->getAdditionalShippingCost(),
            'delivery_time_note_type' => $shipping->getDeliveryTimeNoteType(),
            'delivery_time_in_stock_note' => $shipping->getLocalizedDeliveryTimeInStockNotes(),
            'delivery_time_out_stock_note' => $shipping->getLocalizedDeliveryTimeOutOfStockNotes(),
            'carriers' => $shipping->getCarrierReferences(),
        ];
    }
}
