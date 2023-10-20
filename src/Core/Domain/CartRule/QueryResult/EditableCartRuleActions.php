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

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class EditableCartRuleActions
{
    /**
     * @var bool
     */
    private $freeShipping;

    /**
     * @var EditableCartRuleReduction
     */
    private $reduction;

    /**
     * @var ProductId|null
     */
    private $giftProductId;

    /**
     * @var CombinationId|null
     */
    private $giftCombinationId;

    public function __construct(
        bool $freeShipping,
        EditableCartRuleReduction $reduction,
        ?ProductId $giftProductId,
        ?CombinationId $giftCombinationId
    ) {
        $this->freeShipping = $freeShipping;
        $this->reduction = $reduction;
        $this->giftProductId = $giftProductId;
        $this->giftCombinationId = $giftCombinationId;
    }

    /**
     * @return bool
     */
    public function isFreeShipping(): bool
    {
        return $this->freeShipping;
    }

    /**
     * @return EditableCartRuleReduction
     */
    public function getReduction(): EditableCartRuleReduction
    {
        return $this->reduction;
    }

    /**
     * @return ProductId|null
     */
    public function getGiftProductId(): ?ProductId
    {
        return $this->giftProductId;
    }

    /**
     * @return CombinationId|null
     */
    public function getGiftCombinationId(): ?CombinationId
    {
        return $this->giftCombinationId;
    }
}
