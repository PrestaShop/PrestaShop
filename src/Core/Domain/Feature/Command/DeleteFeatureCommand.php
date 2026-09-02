<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Command;

use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;

class DeleteFeatureCommand
{
    /**
     * @var FeatureId
     */
    private $featureId;

    public function __construct(
        int $featureId
    ) {
        $this->featureId = new FeatureId($featureId);
    }

    /**
     * @return FeatureId
     */
    public function getFeatureId(): FeatureId
    {
        return $this->featureId;
    }
}
