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

namespace PrestaShop\PrestaShop\Core\Domain\Category\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;

/**
 * Class CategoryStatus.
 */
class CategoryStatus
{
    const ENABLED = 'enabled';
    const DISABLED = 'disabled';

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
     *
     * @throws CategoryConstraintException
     */
    public function __construct($status)
    {
        $this->setStatus($status);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->status;
    }

    /**
     * Check if status is equal to other status.
     *
     * @param CategoryStatus $status
     *
     * @return bool
     */
    public function isEqualTo(CategoryStatus $status)
    {
        return $this->getValue() === $status->getValue();
    }

    /**
     * @param string $status
     *
     * @throws CategoryConstraintException
     */
    private function setStatus($status)
    {
        if (!in_array($status, self::AVAILABLE_STATUSES)) {
            throw new CategoryConstraintException(
                sprintf(
                    'Invalid category status %s supplied. Available statuses are "%s"',
                    var_export($status, true),
                    implode(',', self::AVAILABLE_STATUSES)
                ),
                CategoryConstraintException::INVALID_STATUS
            );
        }

        $this->status = $status;
    }
}
