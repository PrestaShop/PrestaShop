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

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\QueryHandler;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryHandler\GetSpecificPriceForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class GetSpecificPriceForEditingHandler implements GetSpecificPriceForEditingHandlerInterface
{
    /**
     * @var SpecificPriceRepository
     */
    private $specificPriceRepository;

    /**
     * @param SpecificPriceRepository $specificPriceRepository
     */
    public function __construct(
        SpecificPriceRepository $specificPriceRepository
    ) {
        $this->specificPriceRepository = $specificPriceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetSpecificPriceForEditing $query): SpecificPriceForEditing
    {
        $specificPrice = $this->specificPriceRepository->get($query->getSpecificPriceId());

        $dateFrom = DateTimeUtil::NULL_VALUE !== $specificPrice->from ? new DateTime($specificPrice->from) : null;
        $dateTo = DateTimeUtil::NULL_VALUE !== $specificPrice->to ? new DateTime($specificPrice->to) : null;

        return new SpecificPriceForEditing(
            (int) $specificPrice->id,
            $specificPrice->reduction_type,
            new DecimalNumber((string) $specificPrice->reduction),
            (bool) $specificPrice->reduction_tax,
            new DecimalNumber((string) $specificPrice->price),
            (int) $specificPrice->from_quantity,
            $dateFrom,
            $dateTo,
            $specificPrice->id_shop_group ?: null,
            $specificPrice->id_shop ?: null,
            $specificPrice->id_cart ?: null,
            $specificPrice->id_currency ?: null,
            $specificPrice->id_specific_price_rule ?: null,
            $specificPrice->id_country ?: null,
            $specificPrice->id_group ?: null,
            $specificPrice->id_customer ?: null
        );
    }
}
