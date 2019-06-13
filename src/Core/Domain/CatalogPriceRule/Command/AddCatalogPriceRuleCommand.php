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

use DateTime;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

/**
 * Adds new catalog price rule with provided data
 */
class AddCatalogPriceRuleCommand
{
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
     * @var Reduction
     */
    private $reduction;

    /**
     * @var bool
     */
    private $includeTax;

    /**
     * @var float
     */
    private $price;

    /**
     * @var DateTime|null
     */
    private $dateTimeFrom;

    /**
     * @var DateTime|null
     */
    private $dateTimeTo;

    /**
     * @param string $name
     * @param int $currencyId
     * @param int $countryId
     * @param int $groupId
     * @param int $fromQuantity
     * @param string $reductionType
     * @param float $reductionValue
     * @param int $shopId
     * @param bool $includeTax
     * @param float $price
     */
    public function __construct(
        $name,
        $currencyId,
        $countryId,
        $groupId,
        $fromQuantity,
        $reductionType,
        $reductionValue,
        $shopId,
        $includeTax,
        $price
    ) {
        $this->name = $name;
        $this->currencyId = $currencyId;
        $this->countryId = $countryId;
        $this->groupId = $groupId;
        $this->fromQuantity = $fromQuantity;
        $this->reduction = new Reduction($reductionType, $reductionValue);
        $this->shopId = $shopId;
        $this->price = $price;
        $this->includeTax = $includeTax;
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
     * @return Reduction
     */
    public function getReduction()
    {
        return $this->reduction;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTimeFrom()
    {
        return $this->dateTimeFrom;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTimeTo()
    {
        return $this->dateTimeTo;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded()
    {
        return $this->includeTax;
    }

    /**
     * @param string $dateTimeFrom
     *
     * @throws CatalogPriceRuleConstraintException
     */
    public function setDateTimeFrom($dateTimeFrom)
    {
        $this->dateTimeFrom = $this->createDateTime($dateTimeFrom);
    }

    /**
     * @param string $dateTimeTo
     *
     * @throws CatalogPriceRuleConstraintException
     */
    public function setDateTimeTo($dateTimeTo)
    {
        $this->dateTimeTo = $this->createDateTime($dateTimeTo);
    }

    /**
     * @param string $dateTime
     *
     * @return DateTime
     *
     * @throws CatalogPriceRuleConstraintException
     */
    private function createDateTime($dateTime)
    {
        try {
            return new DateTime($dateTime);
        } catch (Exception $e) {
            throw new CatalogPriceRuleConstraintException(
                'Invalid date time format',
                CatalogPriceRuleConstraintException::INVALID_DATETIME,
                $e
            );
        }
    }
}
