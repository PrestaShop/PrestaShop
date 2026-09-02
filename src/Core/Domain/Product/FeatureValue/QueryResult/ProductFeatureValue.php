<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\QueryResult;

/**
 * Transfers feature value data in query result
 */
class ProductFeatureValue
{
    /**
     * @var int
     */
    private $featureId;

    /**
     * @var int
     */
    private $featureValueId;

    /**
     * @var string[]
     */
    private $localizedValues;

    /**
     * @var bool
     */
    private $custom;

    /**
     * @param int $featureId
     * @param int $featureValueId
     * @param array $localizedValues
     * @param bool $custom
     */
    public function __construct(
        int $featureId,
        int $featureValueId,
        array $localizedValues,
        bool $custom
    ) {
        $this->featureId = $featureId;
        $this->featureValueId = $featureValueId;
        $this->localizedValues = $localizedValues;
        $this->custom = $custom;
    }

    /**
     * @return int
     */
    public function getFeatureId(): int
    {
        return $this->featureId;
    }

    /**
     * @return int
     */
    public function getFeatureValueId(): int
    {
        return $this->featureValueId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedValues(): array
    {
        return $this->localizedValues;
    }

    /**
     * @return bool
     */
    public function isCustom(): bool
    {
        return $this->custom;
    }
}
