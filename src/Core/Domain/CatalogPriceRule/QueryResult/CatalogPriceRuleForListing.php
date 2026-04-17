<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    private $shopName;

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

    /**
     * @var string|null
     */
    private $currencyIso;

    /**
     * @var bool
     */
    private $isTaxIncluded;

    /**
     * CatalogPriceRuleForListing constructor.
     *
     * @param int $catalogPriceRuleId
     * @param string $catalogPriceRuleName
     * @param int $fromQuantity
     * @param string $reductionType
     * @param DecimalNumber $reduction
     * @param bool $isTaxIncluded
     * @param DateTimeInterface $dateStart
     * @param DateTimeInterface $dateEnd
     * @param string|null $shopName
     * @param string|null $currencyName
     * @param string|null $countryName
     * @param string|null $groupName
     * @param string|null $currencyIso
     */
    public function __construct(
        int $catalogPriceRuleId,
        string $catalogPriceRuleName,
        int $fromQuantity,
        string $reductionType,
        DecimalNumber $reduction,
        bool $isTaxIncluded,
        DateTimeInterface $dateStart,
        DateTimeInterface $dateEnd,
        ?string $shopName,
        ?string $currencyName,
        ?string $countryName,
        ?string $groupName,
        ?string $currencyIso
    ) {
        $this->catalogPriceRuleId = $catalogPriceRuleId;
        $this->catalogPriceRuleName = $catalogPriceRuleName;
        $this->fromQuantity = $fromQuantity;
        $this->reductionType = $reductionType;
        $this->reduction = $reduction;
        $this->isTaxIncluded = $isTaxIncluded;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->shopName = $shopName;
        $this->currencyName = $currencyName;
        $this->countryName = $countryName;
        $this->groupName = $groupName;
        $this->currencyIso = $currencyIso;
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

    /**
     * @return string|null
     */
    public function getShopName(): ?string
    {
        return $this->shopName;
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
     * @return string|null
     */
    public function getCurrencyIso(): ?string
    {
        return $this->currencyIso;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded(): bool
    {
        return $this->isTaxIncluded;
    }
}
