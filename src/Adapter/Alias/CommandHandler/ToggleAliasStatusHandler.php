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

namespace PrestaShop\PrestaShop\Adapter\Alias\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Alias\Repository\AliasRepository;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\UpdateAliasStatusHandler;
use PrestaShop\PrestaShop\Core\Domain\Alias\CommandHandler\ToggleAliasHandlerInterfaces;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\CannotToggleAliasException;
use PrestaShopException;

/**
 * Toggles alias status
 */
class ToggleAliasStatusHandler implements ToggleAliasHandlerInterfaces
{
    /**
     * @var AliasRepository
     */
    private $aliasRepository;

    /**
     * @param AliasRepository $aliasRepository
     */
    public function __construct(
        AliasRepository $aliasRepository
    ) {
        $this->aliasRepository = $aliasRepository;
    }

    /**
     * @param UpdateAliasStatusHandler $command
     *
     * @return void
     *
     * @throws AliasException
     */
    public function handle(UpdateAliasStatusHandler $command): void
    {
        $alias = $this->aliasRepository->get($command->getAliasId());

        try {
            if (false === $alias->toggleStatus()) {
                throw new CannotToggleAliasException(sprintf('Unable to toggle status of alias with id "%d"', $command->getAliasId()->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new AliasException(sprintf('An error occurred when toggling status of alias with id "%d"', $command->getAliasId()->getValue()), 0, $e);
        }
    }
}
