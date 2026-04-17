<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
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
     * @var DecimalNumber
     */
    private $price;

    /**
     * @var ?DateTime
     */
    private $from;

    /**
     * @var ?DateTime
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
     * @param DecimalNumber $price
     * @param Reduction $reduction
     * @param bool $includeTax
     * @param DateTime|null $from
     * @param DateTime|null $to
     */
    public function __construct(
        CatalogPriceRuleId $catalogPriceRuleId,
        string $name,
        int $shopId,
        int $currencyId,
        int $countryId,
        int $groupId,
        int $fromQuantity,
        DecimalNumber $price,
        Reduction $reduction,
        bool $includeTax,
        ?DateTime $from,
        ?DateTime $to
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
     * @return DecimalNumber
     */
    public function getPrice(): DecimalNumber
    {
        return $this->price;
    }

    /**
     * @return DateTime|null
     */
    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    /**
     * @return DateTime|null
     */
    public function getTo(): ?DateTime
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
