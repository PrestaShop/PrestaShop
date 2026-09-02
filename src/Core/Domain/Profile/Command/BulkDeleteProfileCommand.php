<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Command;

use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

/**
 * Class BulkDeleteProfileCommand is a command to bulk delete profiles by given ids.
 */
class BulkDeleteProfileCommand
{
    /**
     * @var ProfileId[]
     */
    private $profileIds = [];

    /**
     * @param array $profileIds
     */
    public function __construct(array $profileIds)
    {
        $this->setProfileIds($profileIds);
    }

    /**
     * @return ProfileId[]
     */
    public function getProfileIds()
    {
        return $this->profileIds;
    }

    /**
     * @param array $profileIds
     */
    private function setProfileIds(array $profileIds)
    {
        foreach ($profileIds as $profileId) {
            $this->profileIds[] = new ProfileId((int) $profileId);
        }
    }
}
