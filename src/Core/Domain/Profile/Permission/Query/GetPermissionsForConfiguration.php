<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Query;

use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

/**
 * Get profile permissons data for configuration
 */
class GetPermissionsForConfiguration
{
    /**
     * @var ProfileId
     */
    private $employeeProfileId;

    /**
     * @param int $employeeProfileId Profile id of employee who configures permissions
     */
    public function __construct(int $employeeProfileId)
    {
        $this->employeeProfileId = new ProfileId($employeeProfileId);
    }

    /**
     * @return ProfileId
     */
    public function getEmployeeProfileId(): ProfileId
    {
        return $this->employeeProfileId;
    }
}
