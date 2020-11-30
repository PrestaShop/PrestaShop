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
        /** @var ProductForEditing $result */
        $result = $this->queryBus->handle(new GetProductForEditing((int) $id));

        return [
            'id' => $id,
            'basic' => [
                'name' => $result->getBasicInformation()->getLocalizedNames(),
                'type' => $result->getBasicInformation()->getType()->getValue(),
                'description' => $result->getBasicInformation()->getLocalizedDescriptions(),
                'description_short' => $result->getBasicInformation()->getLocalizedShortDescriptions(),
            ],
            'price' => [
                'price_tax_excluded' => (float) (string) $result->getPricesInformation()->getPrice(),
                'price_tax_included' => (float) (string) $result->getPricesInformation()->getPrice(),
                'ecotax' => (float) (string) $result->getPricesInformation()->getEcotax(),
                'tax_rules_group_id' => $result->getPricesInformation()->getTaxRulesGroupId(),
                'on_sale' => $result->getPricesInformation()->isOnSale(),
                'wholesale_price' => (float) (string) $result->getPricesInformation()->getWholesalePrice(),
                'unit_price' => (float) (string) $result->getPricesInformation()->getUnitPrice(),
                'unity' => $result->getPricesInformation()->getUnity(),
            ],
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
        ];
    }
}
