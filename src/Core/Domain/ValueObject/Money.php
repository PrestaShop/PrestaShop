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

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;

class Money
{
    /**
     * @var DecimalNumber
     */
    private $amount;

    /**
     * @var CurrencyId
     */
    private $currencyId;

    /**
     * @var bool
     */
    private $taxIncluded;

    /**
     * @param string $amount
     * @param int $currencyId
     * @param bool $taxIncluded
     */
    public function __construct(
        string $amount,
        int $currencyId,
        bool $taxIncluded
    ) {
        $amount = new DecimalNumber($amount);
        if (!$amount->isGreaterOrEqualThanZero()) {
            throw new DomainConstraintException(sprintf('Money amount cannot be lower than zero, %s given', $amount), DomainConstraintException::INVALID_MONEY_AMOUNT);
        }

        $this->amount = $amount;
        $this->currencyId = new CurrencyId($currencyId);
        $this->taxIncluded = $taxIncluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getAmount(): DecimalNumber
    {
        return $this->amount;
    }

    /**
     * @return CurrencyId
     */
    public function getCurrencyId(): CurrencyId
    {
        return $this->currencyId;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded(): bool
    {
        return $this->taxIncluded;
    }
}
