<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Domain\Group\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Group\Exception\InvalidGroupIdException;

/**
 * Class GroupId.
 */
class GroupId
{
    /**
     * @var int
     */
    private $groupId;

    /**
     * @param int $groupId
     *
     * @throws InvalidGroupIdException
     */
    public function __construct($groupId)
    {
        $this->setGroupId($groupId);
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    private function setGroupId($groupId)
    {
        if (!is_numeric($groupId) || 0 >= $groupId) {
            throw new InvalidGroupIdException(
                sprintf('Invalid Group id %s supplied.', var_export($groupId, true))
            );
        }

        $this->groupId = $groupId;
    }
}
