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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult;

class CartRuleRestrictionsForEditing
{
    /**
     * @var bool
     */
    private $countryRestriction;

    /**
     * @var bool
     */
    private $carrierRestriction;

    /**
     * @var bool
     */
    private $groupRestriction;

    /**
     * @var int[]
     */
    private $restrictedCartRuleIds;

    /**
     * @var bool
     */
    private $productRestriction;

    /**
     * @var bool
     */
    private $shopRestriction;

    public function __construct(
        bool $countryRestriction,
        bool $carrierRestriction,
        bool $groupRestriction,
        array $restrictedCartRuleIds,
        bool $productRestriction,
        bool $shopRestriction
    ) {
        $this->countryRestriction = $countryRestriction;
        $this->carrierRestriction = $carrierRestriction;
        $this->groupRestriction = $groupRestriction;
        $this->restrictedCartRuleIds = $restrictedCartRuleIds;
        $this->productRestriction = $productRestriction;
        $this->shopRestriction = $shopRestriction;
    }

    /**
     * @return bool
     */
    public function isCountryRestriction(): bool
    {
        return $this->countryRestriction;
    }

    /**
     * @return bool
     */
    public function isCarrierRestriction(): bool
    {
        return $this->carrierRestriction;
    }

    /**
     * @return bool
     */
    public function isGroupRestriction(): bool
    {
        return $this->groupRestriction;
    }

    /**
     * @return int[]
     */
    public function getRestrictedCartRuleIds(): array
    {
        return $this->restrictedCartRuleIds;
    }

    /**
     * @return bool
     */
    public function isProductRestriction(): bool
    {
        return $this->productRestriction;
    }

    /**
     * @return bool
     */
    public function isShopRestriction(): bool
    {
        return $this->shopRestriction;
    }
}
