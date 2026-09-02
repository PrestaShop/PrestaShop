<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Command;

use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

class BulkDeleteFeatureValueCommand
{
    /**
     * @var FeatureValueId[]
     */
    private readonly array $featureValueIds;

    /**
     * @param int[] $featureValueIds
     */
    public function __construct(array $featureValueIds)
    {
        $this->featureValueIds = array_map(static function ($id): FeatureValueId {
            return new FeatureValueId((int) $id);
        }, $featureValueIds);
    }

    /**
     * @return FeatureValueId[]
     */
    public function getFeatureValueIds(): array
    {
        return $this->featureValueIds;
    }
}
