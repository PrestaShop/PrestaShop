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

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command;

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;

/**
 * Edits catalog price rule with given data
 */
class EditCatalogPriceRuleCommand
{
    /**
     * @var CatalogPriceRuleId
     */
    private $catalogPriceRuleId;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var int|null
     */
    private $shopId;

    /**
     * @var int|null
     */
    private $currencyId;

    /**
     * @var int|null
     */
    private $countryId;

    /**
     * @var int|null
     */
    private $groupId;

    /**
     * @var int|null
     */
    private $fromQuantity;

    /**
     * @var float|null
     */
    private $price;

    /**
     * @var string|null
     */
    private $dateFrom;

    /**
     * @var string|null
     */
    private $dateTo;

    /**
     * @var string|null
     */
    private $reductionType;

    /**
     * @var bool|null
     */
    private $includeTax;

    /**
     * @var float|null
     */
    private $reduction;

    /**
     * @param int $catalogPriceRuleId
     *
     * @throws CatalogPriceRuleConstraintException
     */
    public function __construct($catalogPriceRuleId)
    {
        $this->catalogPriceRuleId = new CatalogPriceRuleId($catalogPriceRuleId);
    }

    /**
     * @return CatalogPriceRuleId
     */
    public function getCatalogPriceRuleId()
    {
        return $this->catalogPriceRuleId;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @param int|null $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * @return int|null
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * @param int|null $currencyId
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
    }

    /**
     * @return int|null
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @param int|null $countryId
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
    }

    /**
     * @return int|null
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int|null $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return int|null
     */
    public function getFromQuantity()
    {
        return $this->fromQuantity;
    }

    /**
     * @param int|null $fromQuantity
     */
    public function setFromQuantity($fromQuantity)
    {
        $this->fromQuantity = $fromQuantity;
    }

    /**
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string|null
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param string|null $dateFrom
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return string|null
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param string|null $dateTo
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return string|null
     */
    public function getReductionType()
    {
        return $this->reductionType;
    }

    /**
     * @param string|null $reductionType
     */
    public function setReductionType($reductionType)
    {
        $this->reductionType = $reductionType;
    }

    /**
     * @return bool|null
     */
    public function isTaxIncluded()
    {
        return $this->includeTax;
    }

    /**
     * @param bool|null $includeTax
     */
    public function setIncludeTax($includeTax)
    {
        $this->includeTax = $includeTax;
    }

    /**
     * @return float|null
     */
    public function getReduction()
    {
        return $this->reduction;
    }

    /**
     * @param float|null $reduction
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;
    }
}
