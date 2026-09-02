<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Query;

use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\InvalidFeatureValueIdException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

/**
 * Retrieves feature value data for editing
 */
class GetFeatureValueForEditing
{
    /**
     * @var FeatureValueId
     */
    private $featureValueId;

    /**
     * @param int $featureValueId
     *
     * @throws InvalidFeatureValueIdException
     */
    public function __construct(int $featureValueId)
    {
        $this->featureValueId = new FeatureValueId($featureValueId);
    }

    /**
     * @return FeatureValueId
     */
    public function getFeatureValueId(): FeatureValueId
    {
        return $this->featureValueId;
    }
}
