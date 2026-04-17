<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileConstraintException;

class ProfileId
{
    /**
     * @var int
     */
    private $profileId;

    /**
     * @throws ProfileConstraintException
     */
    public function __construct(int $profileId)
    {
        $this->assertProfileIdIsGreaterThanZero($profileId);

        $this->profileId = (int) $profileId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->profileId;
    }

    /**
     * @throws ProfileConstraintException
     */
    private function assertProfileIdIsGreaterThanZero(int $profileId)
    {
        if (0 >= $profileId) {
            throw new ProfileConstraintException(
                sprintf('Invalid profile id %s provided', var_export($profileId, true))
            );
        }
    }
}
