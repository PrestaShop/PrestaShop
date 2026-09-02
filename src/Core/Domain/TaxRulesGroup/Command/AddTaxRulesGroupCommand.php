<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;

/**
 * Command responsible for multiple tax rules groups deletion
 */
class AddTaxRulesGroupCommand
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var int[]
     */
    protected $shopAssociation = [];

    /**
     * @param string $name
     * @param bool $enabled
     */
    public function __construct(string $name, bool $enabled)
    {
        $this->name = $name;
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @return self
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function setShopAssociation(array $shopAssociation): self
    {
        if (!$this->assertArrayContainsOnlyIntegerValues($shopAssociation)) {
            throw new TaxRulesGroupConstraintException(
                sprintf(
                    'Given shop association %s must contain only integer values',
                    var_export($shopAssociation, true)
                ),
                TaxRulesGroupConstraintException::INVALID_SHOP_ASSOCIATION
            );
        }

        $this->shopAssociation = $shopAssociation;

        return $this;
    }

    /**
     * @param array $values
     *
     * @return bool
     */
    protected function assertArrayContainsOnlyIntegerValues(array $values): bool
    {
        $filterAllIntegers = function ($value) {
            return is_int($value);
        };

        return !empty($values) && count($values) === count(array_filter($values, $filterAllIntegers));
    }
}
