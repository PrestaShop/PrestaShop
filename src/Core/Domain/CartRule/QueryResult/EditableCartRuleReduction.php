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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class EditableCartRuleReduction
{
    /**
     * @var DecimalNumber
     */
    private $percent;

    /**
     * @var DecimalNumber
     */
    private $amount;

    /**
     * @var bool
     */
    private $tax;

    /**
     * @var CurrencyId|null
     */
    private $currencyId;

    /**
     * @var ProductId|null
     */
    private $productId;

    /**
     * @var bool
     */
    private $excludeSpecial;

    public function __construct(
        DecimalNumber $percent,
        DecimalNumber $amount,
        bool $tax,
        ?CurrencyId $currencyId,
        ?ProductId $productId,
        bool $excludeSpecial
    ) {
        $this->percent = $percent;
        $this->amount = $amount;
        $this->tax = $tax;
        $this->currencyId = $currencyId;
        $this->productId = $productId;
        $this->excludeSpecial = $excludeSpecial;
    }

    /**
     * @return DecimalNumber
     */
    public function getPercent(): DecimalNumber
    {
        return $this->percent;
    }

    /**
     * @return DecimalNumber
     */
    public function getAmount(): DecimalNumber
    {
        return $this->amount;
    }

    /**
     * @return bool
     */
    public function isTax(): bool
    {
        return $this->tax;
    }

    /**
     * @return CurrencyId|null
     */
    public function getCurrencyId(): ?CurrencyId
    {
        return $this->currencyId;
    }

    /**
     * @return ProductId|null
     */
    public function getProductId(): ?ProductId
    {
        return $this->productId;
    }

    /**
     * @return bool
     */
    public function isExcludeSpecial(): bool
    {
        return $this->excludeSpecial;
    }
}
