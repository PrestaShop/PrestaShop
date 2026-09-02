<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\InvalidFeatureValueIdException;

/**
 * Defines FeatureValue ID with it's constraints.
 */
class FeatureValueId
{
    /**
     * @var int
     */
    private $featureValueId;

    /**
     * @param int $featureValueId
     *
     * @throws InvalidFeatureValueIdException
     */
    public function __construct(int $featureValueId)
    {
        $this->assertIsGreaterThanZero($featureValueId);

        $this->featureValueId = $featureValueId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->featureValueId;
    }

    /**
     * @param int $featureValueId
     *
     * @throws InvalidFeatureValueIdException
     */
    private function assertIsGreaterThanZero(int $featureValueId)
    {
        if (0 >= $featureValueId) {
            throw new InvalidFeatureValueIdException(sprintf('Invalid feature id %d supplied. Feature id must be positive integer.', $featureValueId));
        }
    }
}
