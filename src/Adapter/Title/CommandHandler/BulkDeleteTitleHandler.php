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

namespace PrestaShop\PrestaShop\Adapter\Title\CommandHandler;

use Gender;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\BulkDeleteTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\CommandHandler\BulkDeleteTitleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\DeleteTitleException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleNotFoundException;

/**
 * Handles command that bulk delete titles
 */
class BulkDeleteTitleHandler implements BulkDeleteTitleHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteTitleCommand $command): void
    {
        foreach ($command->getTitleIds() as $titleId) {
            $title = new Gender($titleId->getValue());

            if (0 >= $title->id) {
                throw new TitleNotFoundException(sprintf('Unable to find title with id "%d" for deletion', $titleId->getValue()));
            }

            if (!$title->delete()) {
                throw DeleteTitleException::createBulkDeleteFailure($titleId);
            }
        }
    }
}
