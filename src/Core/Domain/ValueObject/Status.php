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

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;

/**
 * Holds data of object status
 */
class Status
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param bool $enabled
     *
     * @throws DomainConstraintException
     */
    public function __construct($enabled)
    {
        $this->assertIsBool($enabled);
        $this->enabled = $enabled;
    }

    /**
     * Checks whether the object is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Validates that value is of type boolean
     *
     * @param $value
     *
     * @throws DomainConstraintException
     */
    private function assertIsBool($value)
    {
        if (!is_bool($value)) {
            throw new DomainConstraintException(
                sprintf('Status must be of type bool, but given "%s"', $value),
                DomainConstraintException::INVALID_STATUS_TYPE
            );
        }
    }
}
