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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;

class SpecificPriceFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param CommandBusInterface $queryBus
     * @param int $contextShopId
     */
    public function __construct(
        CommandBusInterface $queryBus,
        int $contextShopId
    ) {
        $this->queryBus = $queryBus;
        $this->contextShopId = $contextShopId;
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
        $fixedPrice = $specificPriceForEditing->getFixedPrice()->getValue();

        $data = [
            'product_id' => $specificPriceForEditing->getProductId(),
            'groups' => [
                'currency_id' => $specificPriceForEditing->getCurrencyId(),
                'country_id' => $specificPriceForEditing->getCountryId(),
                'group_id' => $specificPriceForEditing->getGroupId(),
                'shop_id' => $specificPriceForEditing->getShopId(),
            ],
            'combination_id' => $specificPriceForEditing->getCombinationId(),
            'from_quantity' => $specificPriceForEditing->getFromQuantity(),
            'date_range' => [
                'from' => $specificPriceForEditing->getDateTimeFrom()->format(DateTime::DEFAULT_DATETIME_FORMAT),
                'to' => $specificPriceForEditing->getDateTimeTo()->format(DateTime::DEFAULT_DATETIME_FORMAT),
            ],
            'impact' => [
                'reduction' => [
                    'type' => $specificPriceForEditing->getReductionType(),
                    'value' => (float) (string) $specificPriceForEditing->getReductionAmount(),
                    'include_tax' => $specificPriceForEditing->includesTax(),
                ],
                'fixed_price_tax_excluded' => (float) (string) $fixedPrice,
            ],
        ];

        if ($customerInfo = $specificPriceForEditing->getCustomerInfo()) {
            $data['customer'] = [
                [
                    'id_customer' => $customerInfo->getId(),
                    'fullname_and_email' => sprintf(
                        '%s %s - %s',
                        $customerInfo->getFirstname(),
                        $customerInfo->getLastname(),
                        $customerInfo->getEmail()
                    ),
                ],
            ];
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultData(): array
    {
        return [
            'from_quantity' => 1,
            'impact' => [
                'reduction' => [
                    'type' => Reduction::TYPE_AMOUNT,
                    'value' => 0,
                    'include_tax' => true,
                ],
                'fixed_price_tax_excluded' => (float) InitialPrice::INITIAL_PRICE_VALUE,
            ],
            'groups' => [
                'shop_id' => $this->contextShopId,
            ],
            'combination_id' => NoCombinationId::NO_COMBINATION_ID,
        ];
    }
}
