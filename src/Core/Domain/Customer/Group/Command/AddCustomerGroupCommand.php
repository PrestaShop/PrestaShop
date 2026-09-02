<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class AddCustomerGroupCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var DecimalNumber
     */
    private $reductionPercent;

    /**
     * @var bool
     */
    private $displayPriceTaxExcluded;

    /**
     * @var bool
     */
    private $showPrice;

    /**
     * @var ShopId[]
     */
    private $shopIds;

    /**
     * @param string[] $localizedNames
     * @param DecimalNumber $reductionPercent
     * @param bool $displayPriceTaxExcluded
     * @param bool $showPrice
     * @param array<int> $shopIds
     */
    public function __construct(
        array $localizedNames,
        DecimalNumber $reductionPercent,
        bool $displayPriceTaxExcluded,
        bool $showPrice,
        array $shopIds
    ) {
        $this->assertReductionIsValid($reductionPercent);

        $this->localizedNames = $localizedNames;
        $this->reductionPercent = $reductionPercent;
        $this->displayPriceTaxExcluded = $displayPriceTaxExcluded;
        $this->showPrice = $showPrice;
        $this->shopIds = array_map(function (int $shopId) {
            return new ShopId($shopId);
        }, $shopIds);
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return bool
     */
    public function displayPriceTaxExcluded(): bool
    {
        return $this->displayPriceTaxExcluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getReductionPercent(): DecimalNumber
    {
        return $this->reductionPercent;
    }

    /**
     * @return bool
     */
    public function showPrice(): bool
    {
        return $this->showPrice;
    }

    /**
     * @return ShopId[]
     */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }

    private function assertReductionIsValid(DecimalNumber $reductionPercent): void
    {
        if ($reductionPercent->isLowerThanZero() || $reductionPercent->isGreaterThan(new DecimalNumber('100'))) {
            throw new GroupConstraintException(
                'Reduction percent must be between 0 and 100',
                GroupConstraintException::INVALID_REDUCTION
            );
        }
    }
}
