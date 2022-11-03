<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;

/**
 * Holds group identification value
 */
class GroupId implements GroupIdInterface
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $groupId
     */
    public function __construct(int $groupId)
    {
        $this->assertValueIsPositive($groupId);
        $this->value = $groupId;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    private function assertValueIsPositive(int $value): void
    {
        if (0 >= $value) {
            throw new GroupConstraintException(sprintf('Group id must be positive integer. "%s" given', $value), GroupConstraintException::INVALID_ID);
        }
    }
}
