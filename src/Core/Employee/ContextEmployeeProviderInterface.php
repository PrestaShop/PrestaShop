<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Employee;

/**
 * Interface ContextEmployeeProviderInterface describes a context employee provider.
 */
interface ContextEmployeeProviderInterface
{
    /**
     * Check if context employee is super admin.
     *
     * @return bool
     */
    public function isSuperAdmin();

    /**
     * Get context employee's ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Get context employee's selected language ID.
     *
     * @return int
     */
    public function getLanguageId();

    /**
     * Get context employee's profile ID.
     *
     * @return int
     */
    public function getProfileId();

    /**
     *  Get context employee's data as an array
     *
     * @return array
     */
    public function getData();
}
