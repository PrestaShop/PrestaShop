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

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult;

use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;

class CatalogPriceRuleForListing
{
    /**
     * @var int
     */
    private $catalogPriceRuleId;

    /**
     * @var string
     */
    private $catalogPriceRuleName;

    /**
     * @var string|null
     */
    private $currencyName;

    /**
     * @var string|null
     */
    private $countryName;

    /**
     * @var string|null
     */
    private $groupName;

    /**
     * @var int
     */
    private $fromQuantity;

    /**
     * @var string
     */
    private $reductionType;

    /**
     * @var DecimalNumber
     */
    private $reduction;

    /**
     * @var DateTimeInterface
     */
    private $dateStart;

    /**
     * @var DateTimeInterface
     */
    private $dateEnd;

    public function __construct(
        int $catalogPriceRuleId,
        string $catalogPriceRuleName,
        ?string $currencyName,
        ?string $countryName,
        ?string $groupName,
        int $fromQuantity,
        string $reductionType,
        DecimalNumber $reduction,
        DateTimeInterface $dateStart,
        DateTimeInterface $dateEnd
    )
   {
       $this->catalogPriceRuleId = $catalogPriceRuleId;
       $this->catalogPriceRuleName = $catalogPriceRuleName;
       $this->currencyName = $currencyName;
       $this->countryName = $countryName;
       $this->groupName = $groupName;
       $this->fromQuantity = $fromQuantity;
       $this->reductionType = $reductionType;
       $this->reduction = $reduction;
       $this->dateStart = $dateStart;
       $this->dateEnd = $dateEnd;
   }

    /**
     * @return int
     */
    public function getCatalogPriceRuleId(): int
    {
        return $this->catalogPriceRuleId;
    }

    /**
     * @return string
     */
    public function getCatalogPriceRuleName(): string
    {
        return $this->catalogPriceRuleName;
    }

    /**
     * @return string|null
     */
    public function getCurrencyName(): ?string
    {
        return $this->currencyName;
    }

    /**
     * @return string|null
     */
    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    /**
     * @return string|null
     */
    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    /**
     * @return int
     */
    public function getFromQuantity(): int
    {
        return $this->fromQuantity;
    }

    /**
     * @return string
     */
    public function getReductionType(): string
    {
        return $this->reductionType;
    }

    /**
     * @return DecimalNumber
     */
    public function getReduction(): DecimalNumber
    {
        return $this->reduction;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateStart(): DateTimeInterface
    {
        return $this->dateStart;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateEnd(): DateTimeInterface
    {
        return $this->dateEnd;
    }
}
