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

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\SetCategoryIsEnabledCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\SetCategoryIsEnabledHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotUpdateCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;

/**
 * @internal
 */
final class SetCategoryIsEnabledHandler implements SetCategoryIsEnabledHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CategoryNotFoundException
     * @throws CannotUpdateCategoryStatusException
     */
    public function handle(SetCategoryIsEnabledCommand $command)
    {
        $categoryId = $command->getCategoryId()->getValue();
        $entity = new Category($categoryId);

        if (!$entity->id) {
            throw new CategoryNotFoundException($command->getCategoryId(), sprintf('Category with id "%s" was not found', $categoryId));
        }

        if (!$entity->toggleStatus()) {
            throw new CannotUpdateCategoryStatusException(sprintf('Cannot update status for category with id "%s"', $categoryId));
        }
    }
}
