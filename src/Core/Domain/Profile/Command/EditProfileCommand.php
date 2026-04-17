<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Command;

use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

/**
 * Edits existing Profile
 */
class EditProfileCommand extends AbstractProfileCommand
{
    /**
     * @var ProfileId
     */
    private $profileId;

    /**
     * @param int $profileId
     * @param string[] $localizedNames
     *
     * @throws ProfileException
     */
    public function __construct($profileId, array $localizedNames)
    {
        parent::__construct($localizedNames);
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
