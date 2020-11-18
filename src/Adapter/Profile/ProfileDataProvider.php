<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
