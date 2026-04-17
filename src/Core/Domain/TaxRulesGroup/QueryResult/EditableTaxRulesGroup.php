<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Transfers tax rules group data for editing
 */
class EditableTaxRulesGroup
{
    /**
     * @var TaxRulesGroupId
     */
    protected $taxRulesGroupId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var int[]
     */
    protected $shopAssociationIds;

    /**
     * @param TaxRulesGroupId $taxRulesGroupId
     * @param string $name
     * @param bool $active
     * @param array<int> $shopAssociationIds
     */
    public function __construct(
        TaxRulesGroupId $taxRulesGroupId,
        string $name,
        bool $active,
        array $shopAssociationIds
    ) {
        $this->taxRulesGroupId = $taxRulesGroupId;
        $this->name = $name;
        $this->active = $active;
        $this->shopAssociationIds = $shopAssociationIds;
    }

    /**
     * @return TaxRulesGroupId
     */
    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
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
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return int[]
     */
    public function getShopAssociationIds(): array
    {
        return $this->shopAssociationIds;
    }
}
