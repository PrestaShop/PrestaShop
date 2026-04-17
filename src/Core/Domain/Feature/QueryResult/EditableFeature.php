<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;

/**
 * Stores feature data that's needed for editing.
 */
class EditableFeature
{
    /**
     * @var FeatureId
     */
    private $featureId;

    /**
     * @var string[]
     */
    private $name;

    /**
     * @var int[]
     */
    private $shopAssociationIds;

    /**
     * @param FeatureId $featureId
     * @param string[] $name
     * @param int[] $shopAssociationIds
     */
    public function __construct(FeatureId $featureId, array $name, array $shopAssociationIds)
    {
        $this->featureId = $featureId;
        $this->name = $name;
        $this->shopAssociationIds = $shopAssociationIds;
    }

    /**
     * @return FeatureId
     */
    public function getFeatureId()
    {
        return $this->featureId;
    }

    /**
     * @return string[]
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int[]
     */
    public function getShopAssociationIds()
    {
        return $this->shopAssociationIds;
    }
}
