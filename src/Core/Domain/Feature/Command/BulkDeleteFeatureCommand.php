<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Command;

use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;

class BulkDeleteFeatureCommand
{
    /**
     * @var FeatureId[]
     */
    private $featureIds;

    /**
     * @param int[] $featureIds
     */
    public function __construct(array $featureIds)
    {
        foreach ($featureIds as $featureId) {
            $this->featureIds[] = new FeatureId($featureId);
        }
    }

    /**
     * @return FeatureId[]
     */
    public function getFeatureIds(): array
    {
        return $this->featureIds;
    }
}
