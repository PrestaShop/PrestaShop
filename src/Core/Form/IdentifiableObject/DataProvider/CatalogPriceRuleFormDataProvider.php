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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\EditableCatalogPriceRule;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

/**
 * Provides data for catalog price rule add/edit forms
 */
final class CatalogPriceRuleFormDataProvider implements FormDataProviderInterface
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
    public function getData($catalogPriceRuleId)
    {
        /** @var EditableCatalogPriceRule $editableCatalogPriceRule */
        $editableCatalogPriceRule = $this->queryBus->handle(new GetCatalogPriceRuleForEditing((int) $catalogPriceRuleId));

        $dateTimeFormat = 'Y-m-d H:i:s';
        $price = $editableCatalogPriceRule->getPrice();
        $leaveInitialPrice = false;
        $from = $editableCatalogPriceRule->getFrom();
        $to = $editableCatalogPriceRule->getTo();

        if ($price->isLowerOrEqualThanZero()) {
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
            'price' => null === $price ? $price : (string) $price,
            'leave_initial_price' => $leaveInitialPrice,
            'date_range' => [
                'from' => $from ? $from->format($dateTimeFormat) : '',
                'to' => $to ? $to->format($dateTimeFormat) : '',
            ],
            'reduction' => [
                'type' => $editableCatalogPriceRule->getReduction()->getType(),
                'value' => (string) $editableCatalogPriceRule->getReduction()->getValue(),
                'include_tax' => $editableCatalogPriceRule->isTaxIncluded(),
            ],
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
            'reduction' => [
                'type' => Reduction::TYPE_AMOUNT,
                'value' => 0,
                'include_tax' => true,
            ],
        ];
    }
}
