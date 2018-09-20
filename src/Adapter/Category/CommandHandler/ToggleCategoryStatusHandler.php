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

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\ToggleCategoryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\ToggleCategoryStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotUpdateCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;

/**
 * Class ToggleCategoryStatusHandler.
 *
 * @internal
 */
final class ToggleCategoryStatusHandler implements ToggleCategoryStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CategoryNotFoundException
     * @throws CannotUpdateCategoryStatusException
     */
    public function handle(ToggleCategoryStatusCommand $command)
    {
        $entity = new Category($command->getCategoryId()->getValue());

        if (!$entity->id) {
            throw new CategoryNotFoundException(
                sprintf('Category with id "%s" was not found', $command->getCategoryId()->getValue())
            );
        }

        if (!$entity->toggleStatus()) {
            throw new CannotUpdateCategoryStatusException(sprintf(
                'Cannot update status for category with id "%s"',
                $command->getCategoryId()->getValue()
            ));
        }
    }
}
