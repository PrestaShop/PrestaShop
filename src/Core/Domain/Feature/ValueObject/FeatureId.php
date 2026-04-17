<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;

/**
 * Defines Feature ID with its constraints.
 */
class FeatureId
{
    /**
     * @var int
     */
    private $featureId;

    /**
     * @param int $featureId
     */
    public function __construct(int $featureId)
    {
        $this->assertIsGreaterThanZero($featureId);
        $this->featureId = $featureId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->featureId;
    }

    /**
     * @param int $featureId
     *
     * @throws FeatureConstraintException
     */
    private function assertIsGreaterThanZero(int $featureId): void
    {
        if (0 >= $featureId) {
            throw new FeatureConstraintException(
                sprintf('Invalid feature id %d. It must be greater than zero.', $featureId),
                FeatureConstraintException::INVALID_ID
            );
        }
    }
}
