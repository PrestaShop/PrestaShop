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

namespace PrestaShop\PrestaShop\Adapter\Discount\Repository;

use CartRule;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Discount\Validate\DiscountValidator;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotAddDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * This repository is used for the new Discount domain, but it still relies on the legacy CartRule ObjectModel.
 */
class DiscountRepository extends AbstractObjectModelRepository
{
    public function __construct(
        protected readonly DiscountValidator $cartRuleValidator,
        protected readonly Connection $connection,
        protected readonly string $dbPrefix
    ) {
    }

    public function add(CartRule $cartRule): CartRule
    {
        $this->cartRuleValidator->validate($cartRule);
        $this->addObjectModel($cartRule, CannotAddDiscountException::class);

        return $cartRule;
    }

    public function get(DiscountId $discountId): CartRule
    {
        /** @var CartRule $cartRule */
        $cartRule = $this->getObjectModel(
            $discountId->getValue(),
            CartRule::class,
            DiscountNotFoundException::class
        );

        return $cartRule;
    }
}
