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

namespace PrestaShop\PrestaShop\Adapter\SpecificPrice\QueryHandler;

use DateTime;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Adapter\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Query\GetSpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\QueryHandler\GetSpecificPriceForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\QueryResult\SpecificPriceForEditing;

/**
 * Handles @see GetSpecificPriceForEditing using legacy object model
 */
final class GetSpecificPriceForEditingHandler implements GetSpecificPriceForEditingHandlerInterface
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
     * @param GetSpecificPriceForEditing $query
     *
     * @return SpecificPriceForEditing
     */
    public function handle(GetSpecificPriceForEditing $query): SpecificPriceForEditing
    {
        $specificPrice = $this->specificPriceRepository->get($query->getSpecificPriceId());

        return new SpecificPriceForEditing(
            (int) $specificPrice->id_product,
            $specificPrice->reduction_type,
            new Number((string) $specificPrice->reduction),
            (bool) $specificPrice->reduction_tax,
            new Number($specificPrice->price),
            (int) $specificPrice->from_quantity,
            (int) $specificPrice->id_shop_group,
            (int) $specificPrice->id_shop,
            (int) $specificPrice->id_cart,
            (int) $specificPrice->id_product_attribute,
            (int) $specificPrice->id_currency,
            (int) $specificPrice->id_specific_price_rule,
            (int) $specificPrice->id_country,
            (int) $specificPrice->id_group,
            (int) $specificPrice->id_customer,
            new DateTime($specificPrice->from),
            new DateTime($specificPrice->to)
        );
    }
}
