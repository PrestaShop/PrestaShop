<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CartRule\CommandHandler;

use PrestaShop\PrestaShop\Adapter\CartRule\AbstractCartRuleHandler;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\ToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\CommandHandler\ToggleCartRuleStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\UpdateCartRuleException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\ToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\ToggleManufacturerStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\UpdateManufacturerException;

/**
 * Handles command which toggles cart rule status
 */
final class ToggleCartRuleStatusHandler extends AbstractCartRuleHandler implements ToggleCartRuleStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ToggleCartRuleStatusCommand $command)
    {
        $cartRule = $this->getCartRule($command->getCartRuleId());

        if (!$this->toggleCartRuleStatus($cartRule, $command->getExpectedStatus())) {
            throw new UpdateCartRuleException(sprintf('Unable to toggle cart rule status with id "%s"', $cartRule->id), UpdateCartRuleException::FAILED_UPDATE_STATUS);
        }
    }
}
