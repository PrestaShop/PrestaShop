<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;

/**
 * Provides data for editing CatalogPriceRule
 */
class EditableCatalogPriceRule
{
    /**
     * @var CatalogPriceRuleId
     */
    private $catalogPriceRuleId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var int
     */
    private $currencyId;

    /**
     * @var int
     */
    private $countryId;

    /**
     * @var int
     */
    private $groupId;

    /**
     * @var int
     */
    private $fromQuantity;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $reductionType;
    /**
     * @var bool
     */
    private $includeTax;

    /**
     * @var float
     */
    private $reduction;

    /**
     * @param int $catalogPriceRuleId
     * @param string $name
     * @param int $shopId
     * @param int $currencyId
     * @param int $countryId
     * @param int $groupId
     * @param int $fromQuantity
     * @param float $price
     * @param string $from
     * @param string $to
     * @param string $reductionType
     * @param bool $includeTax
     * @param float $reduction
     */
    public function __construct(
        $catalogPriceRuleId,
        $name,
        $shopId,
        $currencyId,
        $countryId,
        $groupId,
        $fromQuantity,
        $price,
        $from,
        $to,
        $reductionType,
        $includeTax,
        $reduction
    ) {
        $this->catalogPriceRuleId = $catalogPriceRuleId;
        $this->name = $name;
        $this->shopId = $shopId;
        $this->currencyId = $currencyId;
        $this->countryId = $countryId;
        $this->groupId = $groupId;
        $this->fromQuantity = $fromQuantity;
        $this->price = $price;
        $this->from = $from;
        $this->to = $to;
        $this->reductionType = $reductionType;
        $this->includeTax = $includeTax;
        $this->reduction = $reduction;
    }

    /**
     * @return CatalogPriceRuleId
     */
    public function getCatalogPriceRuleId()
    {
        return $this->catalogPriceRuleId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @return int
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * @return int
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @return int
     */
    public function getFromQuantity()
    {
        return $this->fromQuantity;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getReductionType()
    {
        return $this->reductionType;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded()
    {
        return $this->includeTax;
    }

    /**
     * @return float
     */
    public function getReduction()
    {
        return $this->reduction;
    }
}
