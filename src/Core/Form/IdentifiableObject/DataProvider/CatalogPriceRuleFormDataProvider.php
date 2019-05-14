<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\editableCatalogPriceRule;

/**
 * Provides data for catalog price rule add/edit forms
 */
final class CatalogPriceRuleFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @param CommandBusInterface $bus
     */
    public function __construct(
        CommandBusInterface $bus
    ) {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($catalogPriceRuleId)
    {
        /** @var editableCatalogPriceRule $editableCatalogPriceRule */
        $editableCatalogPriceRule = $this->bus->handle(new GetCatalogPriceRuleForEditing((int) $catalogPriceRuleId));

        $price = $editableCatalogPriceRule->getPrice();
        $leaveInitialPrice = false;

        if (0 > $price) {
            $price = null;
            $leaveInitialPrice = true;
        }

        $data = [
            'name' => $editableCatalogPriceRule->getName(),
            'id_shop' => $editableCatalogPriceRule->getShopId(),
            'id_currency' => $editableCatalogPriceRule->getCurrencyId(),
            'id_country' => $editableCatalogPriceRule->getCountryId(),
            'id_group' => $editableCatalogPriceRule->getGroupId(),
            'from_quantity' => $editableCatalogPriceRule->getFromQuantity(),
            'price' => $price,
            'leave_initial_price' => $leaveInitialPrice,
            'from' => $editableCatalogPriceRule->getFrom(),
            'to' => $editableCatalogPriceRule->getTo(),
            'reduction_type' => $editableCatalogPriceRule->getReductionType(),
            'include_tax' => $editableCatalogPriceRule->isTaxIncluded(),
            'reduction' => $editableCatalogPriceRule->getReduction(),
        ];

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'from_quantity' => 1,
            'leave_initial_price' => true,
            'reduction' => 0,
        ];
    }
}
