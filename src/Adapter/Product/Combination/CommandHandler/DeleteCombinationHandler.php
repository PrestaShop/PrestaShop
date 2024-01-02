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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationDeleter;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\DeleteCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\DeleteCombinationHandlerInterface;

/**
 * Handles @see DeleteCombinationCommand using adapter udpater service
 */
#[AsCommandHandler]
class DeleteCombinationHandler implements DeleteCombinationHandlerInterface
{
    /**
     * @var CombinationDeleter
     */
    private $combinationDeleter;

    /**
     * @param CombinationDeleter $combinationDeleter
     */
    public function __construct(CombinationDeleter $combinationDeleter)
    {
        $this->combinationDeleter = $combinationDeleter;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(DeleteCombinationCommand $command): void
    {
        $this->combinationDeleter->deleteCombination($command->getCombinationId(), $command->getShopConstraint());
    }
}
