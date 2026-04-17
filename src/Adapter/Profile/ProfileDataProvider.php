<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile;

use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use Profile;

/**
 * Class ProfileDataProvider provides employee profile data using legacy logic.
 */
class ProfileDataProvider
{
    /**
     * @var ContextEmployeeProviderInterface
     */
    private $contextEmployeeProvider;

    /**
     * @var int
     */
    private $superAdminProfileId;

    /**
     * @param ContextEmployeeProviderInterface $contextEmployeeProvider
     * @param int $superAdminProfileId
     */
    public function __construct(
        ContextEmployeeProviderInterface $contextEmployeeProvider,
        $superAdminProfileId
    ) {
        $this->contextEmployeeProvider = $contextEmployeeProvider;
        $this->superAdminProfileId = $superAdminProfileId;
    }

    /**
     * Get employee profiles.
     *
     * @param int $languageId
     *
     * @return array
     */
    public function getProfiles($languageId)
    {
        $profiles = Profile::getProfiles($languageId);

        if ($profiles && !$this->contextEmployeeProvider->isSuperAdmin()) {
            foreach ($profiles as $key => $profile) {
                if ($profile['id_profile'] == $this->superAdminProfileId) {
                    unset($profiles[$key]);
                    break;
                }
            }
        }

        return $profiles ?: [];
    }
}
