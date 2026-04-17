<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Command;

use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

/**
 * Class DeleteProfileCommand is a command to delete profile by given id.
 */
class DeleteProfileCommand
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
