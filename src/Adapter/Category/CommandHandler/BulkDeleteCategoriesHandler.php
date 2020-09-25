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

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkDeleteCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\BulkDeleteCategoriesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteRootCategoryForShopException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\FailedToDeleteCategoryException;

/**
 * Class BulkDeleteCategoriesHandler.
 */
final class BulkDeleteCategoriesHandler extends AbstractDeleteCategoryHandler implements BulkDeleteCategoriesHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CategoryNotFoundException
     * @throws CannotDeleteRootCategoryForShopException
     * @throws FailedToDeleteCategoryException
     */
    public function handle(BulkDeleteCategoriesCommand $command)
    {
        foreach ($command->getCategoryIds() as $categoryId) {
            $category = new Category($categoryId->getValue());

            if (!$category->id) {
                throw new CategoryNotFoundException($categoryId, sprintf('Category with id %s cannot be found.', var_export($categoryId->getValue(), true)));
            }

            if ($category->isRootCategoryForAShop()) {
                throw new CannotDeleteRootCategoryForShopException(sprintf('Shop\'s root category with id %s cannot be deleted.', var_export($categoryId->getValue(), true)));
            }

            if (!$category->delete()) {
                throw new FailedToDeleteCategoryException(sprintf('Failed to delete category with id %s', var_export($categoryId->getValue(), true)));
            }

            $this->handleProductsUpdate((int) $category->id_parent, $command->getDeleteMode());
        }
    }
}
