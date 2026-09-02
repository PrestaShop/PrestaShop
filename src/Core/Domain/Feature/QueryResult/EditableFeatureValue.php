<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

/**
 * Stores feature value data that's needed for editing.
 */
class EditableFeatureValue
{
    /**
     * @var FeatureValueId
     */
    private $featureValueId;

    /**
     * @var FeatureId
     */
    private $featureId;

    /**
     * @var string[]
     */
    private $localizedValues;

    /**
     * @param FeatureValueId $featureValueId
     * @param FeatureId $featureId
     * @param string[] $localizedValues
     */
    public function __construct(
        FeatureValueId $featureValueId,
        FeatureId $featureId,
        array $localizedValues
    ) {
        $this->featureValueId = $featureValueId;
        $this->featureId = $featureId;
        $this->localizedValues = $localizedValues;
    }

    /**
     * @return FeatureValueId
     */
    public function getFeatureValueId(): FeatureValueId
    {
        return $this->featureValueId;
    }

    /**
     * @return FeatureId
     */
    public function getFeatureId(): FeatureId
    {
        return $this->featureId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedValues(): array
    {
        return $this->localizedValues;
    }
}
