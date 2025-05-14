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

namespace PrestaShop\PrestaShop\Adapter\Discount\QueryHandler;

use DateTimeImmutable;
use Exception;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountForEditing;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryHandler\GetDiscountForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult\DiscountForEditing;

#[AsQueryHandler]
class GetDiscountForEditingHandler implements GetDiscountForEditingHandlerInterface
{
    public function __construct(
        protected readonly DiscountRepository $discountRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function handle(GetDiscountForEditing $query): DiscountForEditing
    {
        $cartRule = $this->discountRepository->get($query->discountId);

        return new DiscountForEditing(
            $query->discountId->getValue(),
            $cartRule->priority,
            $cartRule->active,
            new DateTimeImmutable($cartRule->date_from),
            new DateTimeImmutable($cartRule->date_to),
            $cartRule->quantity,
            $cartRule->quantity_per_user,
            $cartRule->description,
            $cartRule->code,
            (int) $cartRule->id_customer,
            $cartRule->highlight,
            $cartRule->partial_use,
            $cartRule->type,
            new DecimalNumber($cartRule->reduction_percent),
            new DecimalNumber($cartRule->reduction_amount),
            $cartRule->reduction_currency,
            $cartRule->reduction_tax,
            $cartRule->reduction_product,
            $cartRule->gift_product,
            $cartRule->gift_product_attribute,
        );
    }
}
