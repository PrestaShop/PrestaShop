<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

/**
 * Transfers feature value data in command
 */
class ProductFeatureValue
{
    /**
     * @var FeatureId
     */
    private $featureId;

    /**
     * @var FeatureValueId|null
     */
    private $featureValueId;

    /**
     * @var string[]|null
     */
    private $localizedCustomValues;

    /**
     * @param int $featureId
     * @param int|null $featureValueId
     * @param array|null $localizedCustomValues
     */
    public function __construct(int $featureId, ?int $featureValueId = null, ?array $localizedCustomValues = null)
    {
        $this->featureId = new FeatureId($featureId);
        $this->featureValueId = null !== $featureValueId ? new FeatureValueId($featureValueId) : null;
        $this->localizedCustomValues = $localizedCustomValues;
    }

    /**
     * @return FeatureId
     */
    public function getFeatureId(): FeatureId
    {
        return $this->featureId;
    }

    /**
     * @return FeatureValueId|null
     */
    public function getFeatureValueId(): ?FeatureValueId
    {
        return $this->featureValueId;
    }

    /**
     * @param FeatureValueId $featureValueId
     */
    public function setFeatureValueId(FeatureValueId $featureValueId): void
    {
        $this->featureValueId = $featureValueId;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedCustomValues(): ?array
    {
        return $this->localizedCustomValues;
    }
}
