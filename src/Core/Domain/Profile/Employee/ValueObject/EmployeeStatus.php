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

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\UndefinedEmployeeStatusException;

/**
 * Class EmployeeStatus.
 */
class EmployeeStatus
{
    const ENABLED = 'enabled';
    const DISABLED = 'disabled';

    /**
     * @internal
     */
    const AVAILABLE_STATUSES = [
        self::ENABLED,
        self::DISABLED,
    ];

    /**
     * @var string
     */
    private $status;

    /**
     * @param string $status
     */
    public function __construct($status)
    {
        $this->assertStatusExists($status);

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getStatus() === self::ENABLED;
    }

    /**
     * @param string $status
     */
    private function assertStatusExists($status)
    {
        if (!in_array($status, self::AVAILABLE_STATUSES)) {
            throw new UndefinedEmployeeStatusException(sprintf(
                sprintf(
                    'Undefined employee status %s supplied. Supported statuses are: "%s"',
                    var_export($status, true),
                    implode(',', self::AVAILABLE_STATUSES)
                )
            ));
        }
    }
}
