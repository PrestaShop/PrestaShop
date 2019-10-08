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

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

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
     * @var DateTime
     */
    private $from;

    /**
     * @var DateTime
     */
    private $to;

    /**
     * @var bool
     */
    private $includeTax;

    /**
     * @var Reduction
     */
    private $reduction;

    /**
     * @param CatalogPriceRuleId $catalogPriceRuleId
     * @param string $name
     * @param int $shopId
     * @param int $currencyId
     * @param int $countryId
     * @param int $groupId
     * @param int $fromQuantity
     * @param float $price
     * @param DateTime $from
     * @param DateTime $to
     * @param Reduction $reduction
     * @param bool $includeTax
     */
    public function __construct(
        CatalogPriceRuleId $catalogPriceRuleId,
        string $name,
        int $shopId,
        int $currencyId,
        int $countryId,
        int $groupId,
        int $fromQuantity,
        float $price,
        DateTime $from,
        DateTime $to,
        Reduction $reduction,
        bool $includeTax
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
        $this->reduction = $reduction;
        $this->includeTax = $includeTax;
    }

    /**
     * @return CatalogPriceRuleId
     */
    public function getCatalogPriceRuleId(): CatalogPriceRuleId
    {
        return $this->catalogPriceRuleId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->countryId;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @return int
     */
    public function getFromQuantity(): int
    {
        return $this->fromQuantity;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return DateTime
     */
    public function getFrom(): DateTime
    {
        return $this->from;
    }

    /**
     * @return DateTime
     */
    public function getTo(): DateTime
    {
        return $this->to;
    }

    /**
     * @return Reduction
     */
    public function getReduction(): Reduction
    {
        return $this->reduction;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded(): bool
    {
        return $this->includeTax;
    }
}
