<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command;

use DateTime;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

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
     * @var DateTime|null
     */
    private $dateTimeFrom;

    /**
     * @var DateTime|null
     */
    private $dateTimeTo;

    /**
     * @var bool|null
     */
    private $includeTax;

    /**
     * @var Reduction
     */
    private $reduction;

    /**
     * @param int $catalogPriceRuleId
     *
     * @throws CatalogPriceRuleConstraintException
     */
    public function __construct(int $catalogPriceRuleId)
    {
        $this->catalogPriceRuleId = new CatalogPriceRuleId($catalogPriceRuleId);
    }

    /**
     * @return CatalogPriceRuleId
     */
    public function getCatalogPriceRuleId(): CatalogPriceRuleId
    {
        return $this->catalogPriceRuleId;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     */
    public function setShopId(int $shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * @return int|null
     */
    public function getCurrencyId(): ?int
    {
        return $this->currencyId;
    }

    /**
     * @param int $currencyId
     */
    public function setCurrencyId(int $currencyId)
    {
        $this->currencyId = $currencyId;
    }

    /**
     * @return int|null
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     */
    public function setCountryId(int $countryId)
    {
        $this->countryId = $countryId;
    }

    /**
     * @return int|null
     */
    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId(int $groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return int|null
     */
    public function getFromQuantity(): ?int
    {
        return $this->fromQuantity;
    }

    /**
     * @param int $fromQuantity
     */
    public function setFromQuantity(int $fromQuantity)
    {
        $this->fromQuantity = $fromQuantity;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTimeFrom(): ?DateTime
    {
        return $this->dateTimeFrom;
    }

    /**
     * @param string $dateTimeFrom
     *
     * @throws CatalogPriceRuleConstraintException
     */
    public function setDateTimeFrom(string $dateTimeFrom)
    {
        $this->dateTimeFrom = $this->createDateTime($dateTimeFrom);
    }

    /**
     * @return DateTime|null
     */
    public function getDateTimeTo(): ?DateTime
    {
        return $this->dateTimeTo;
    }

    /**
     * @param string $dateTimeTo
     *
     * @throws CatalogPriceRuleConstraintException
     */
    public function setDateTimeTo(string $dateTimeTo)
    {
        $this->dateTimeTo = $this->createDateTime($dateTimeTo);
    }

    /**
     * @return bool|null
     */
    public function isTaxIncluded(): ?bool
    {
        return $this->includeTax;
    }

    /**
     * @param bool $includeTax
     */
    public function setIncludeTax(bool $includeTax)
    {
        $this->includeTax = $includeTax;
    }

    /**
     * @return Reduction|null
     */
    public function getReduction(): ?Reduction
    {
        return $this->reduction;
    }

    /**
     * @param string $type
     * @param string $value
     */
    public function setReduction(string $type, string $value)
    {
        $this->reduction = new Reduction($type, $value);
    }

    /**
     * @param string $dateTime
     *
     * @return DateTime
     */
    private function createDateTime(string $dateTime)
    {
        try {
            return new DateTime($dateTime);
        } catch (Exception $e) {
            throw new CatalogPriceRuleConstraintException('Invalid date time format', CatalogPriceRuleConstraintException::INVALID_DATETIME, $e);
        }
    }
}
