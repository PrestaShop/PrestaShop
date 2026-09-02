<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Query;

use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

/**
 * Get Profile data for editing
 */
class GetProfileForEditing
{
    /**
     * @var ProfileId
     */
    private $profileId;

    /**
     * @param int $profileId
     */
    public function __construct($profileId)
    {
        $this->profileId = new ProfileId((int) $profileId);
    }

    /**
     * @return ProfileId
     */
    public function getProfileId()
    {
        return $this->profileId;
    }
}
