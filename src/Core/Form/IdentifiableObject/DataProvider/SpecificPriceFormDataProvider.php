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
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;

class SpecificPriceFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(
        CommandBusInterface $queryBus
    ) {
        $this->queryBus = $queryBus;
    }

    /**
     * @param int $id
     *
     * @return array<string, mixed>
     */
    public function getData($id): array
    {
        /** @var SpecificPriceForEditing $specificPriceForEditing */
        $specificPriceForEditing = $this->queryBus->handle(new GetSpecificPriceForEditing((int) $id));
        $fixedPrice = $specificPriceForEditing->getPrice();

        return [
            'product_id' => $specificPriceForEditing->getProductId(),
            'combination_id' => $specificPriceForEditing->getCombinationId(),
            'currency_id' => $specificPriceForEditing->getCurrencyId(),
            'country_id' => $specificPriceForEditing->getCountryId(),
            'group_id' => $specificPriceForEditing->getGroupId(),
            'customer_id' => $specificPriceForEditing->getCustomerId(),
            'from_quantity' => $specificPriceForEditing->getFromQuantity(),
            'price' => (string) $fixedPrice,
            'leave_initial_price' => $fixedPrice->equalsZero(),
            'date_range' => [
                'from' => $specificPriceForEditing->getDateTimeFrom()->format(DateTime::DEFAULT_DATETIME_FORMAT),
                'to' => $specificPriceForEditing->getDateTimeTo()->format(DateTime::DEFAULT_DATETIME_FORMAT),
            ],
            'reduction' => [
                'type' => $specificPriceForEditing->getReductionType(),
                'value' => (string) $specificPriceForEditing->getReductionAmount(),
            ],
            'include_tax' => $specificPriceForEditing->includesTax(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultData(): array
    {
        return [
            'reduction' => [
                'value' => 0,
            ],
        ];
    }
}
