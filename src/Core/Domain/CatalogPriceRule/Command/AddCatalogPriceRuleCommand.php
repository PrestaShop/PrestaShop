<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command;

use DateTime;
use Exception;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
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
     * @var DecimalNumber
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
     * @param string $reductionValue
     * @param int $shopId
     * @param bool $includeTax
     * @param float $price
     *
     * @throws DomainConstraintException
     */
    public function __construct(
        string $name,
        int $currencyId,
        int $countryId,
        int $groupId,
        int $fromQuantity,
        string $reductionType,
        string $reductionValue,
        int $shopId,
        bool $includeTax,
        float $price
    ) {
        $this->name = $name;
        $this->currencyId = $currencyId;
        $this->countryId = $countryId;
        $this->groupId = $groupId;
        $this->fromQuantity = $fromQuantity;
        $this->reduction = new Reduction($reductionType, $reductionValue);
        $this->shopId = $shopId;
        $this->price = new DecimalNumber((string) $price);
        $this->includeTax = $includeTax;
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
     * @return Reduction
     */
    public function getReduction(): Reduction
    {
        return $this->reduction;
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
    public function getDateTimeFrom(): ?DateTime
    {
        return $this->dateTimeFrom;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTimeTo(): ?DateTime
    {
        return $this->dateTimeTo;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded(): bool
    {
        return $this->includeTax;
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
     * @param string $dateTimeTo
     *
     * @throws CatalogPriceRuleConstraintException
     */
    public function setDateTimeTo(string $dateTimeTo)
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
    private function createDateTime(string $dateTime): DateTime
    {
        try {
            return new DateTime($dateTime);
        } catch (Exception $e) {
            throw new CatalogPriceRuleConstraintException('An error occured when creating DateTime object for catalog price rule', CatalogPriceRuleConstraintException::INVALID_DATETIME, $e);
        }
    }
}
