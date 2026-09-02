<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Command;

use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\InvalidFeatureValueIdException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

/**
 * Class EditFeatureValueCommand is used to edit FeatureValue content
 */
class EditFeatureValueCommand
{
    /**
     * @var FeatureValueId
     */
    private $featureValueId;

    /**
     * @var FeatureId|null
     */
    private $featureId;

    /**
     * @var string[]|null
     */
    private $localizedValues;

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

    /**
     * @return FeatureId|null
     */
    public function getFeatureId(): ?FeatureId
    {
        return $this->featureId;
    }

    /**
     * @param int $featureId
     *
     * @return $this
     */
    public function setFeatureId(int $featureId): self
    {
        $this->featureId = new FeatureId($featureId);

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedValues(): ?array
    {
        return $this->localizedValues;
    }

    /**
     * @param array $localizedValues
     *
     * @return $this
     */
    public function setLocalizedValues(array $localizedValues): self
    {
        $this->localizedValues = $localizedValues;

        return $this;
    }
}
