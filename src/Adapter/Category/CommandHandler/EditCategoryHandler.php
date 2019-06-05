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

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\EditCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotEditCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;

/**
 * Class EditCategoryHandler.
 *
 * @internal
 */
final class EditCategoryHandler extends AbstractObjectModelHandler implements EditCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CategoryNotFoundException
     * @throws CannotEditCategoryException
     */
    public function handle(EditCategoryCommand $command)
    {
        $category = new Category($command->getCategoryId()->getValue());

        if (!$category->id) {
            throw new CategoryNotFoundException(
                $command->getCategoryId(),
                sprintf('Category with id "%s" cannot be found.', $command->getCategoryId()->getValue())
            );
        }

        $this->updateCategoryFromCommandData($category, $command);
    }

    /**
     * Updates legacy object model with data from command
     *
     * @param Category $category
     * @param EditCategoryCommand $command
     */
    private function updateCategoryFromCommandData(Category $category, EditCategoryCommand $command)
    {
        if (null !== $command->isActive()) {
            $category->active = $command->isActive();
        }

        if (null !== $command->getParentCategoryId()) {
            $category->id_parent = $command->getParentCategoryId();
        }

        if (null !== $command->getLocalizedNames()) {
            $category->name = $command->getLocalizedNames();
        }

        if (null !== $command->getLocalizedLinkRewrites()) {
            $category->link_rewrite = $command->getLocalizedLinkRewrites();
        }

        if (null !== $command->getLocalizedDescriptions()) {
            $category->description = $command->getLocalizedDescriptions();
        }

        if (null !== $command->getLocalizedMetaTitles()) {
            $category->meta_title = $command->getLocalizedMetaTitles();
        }

        if (null !== $command->getLocalizedMetaDescriptions()) {
            $category->meta_description = $command->getLocalizedMetaDescriptions();
        }

        if (null !== $command->getLocalizedMetaKeywords()) {
            $category->meta_keywords = $command->getLocalizedMetaKeywords();
        }

        if (null !== $command->getAssociatedGroupIds()) {
            $category->groupBox = $command->getAssociatedGroupIds();
        }

        if ($command->getAssociatedShopIds()) {
            $this->associateWithShops($category, $command->getAssociatedShopIds());
        }

        if (false === $category->validateFields(false)) {
            throw new CategoryException('Invalid data when updating category');
        }

        if (false === $category->validateFieldsLang(false)) {
            throw new CategoryException('Invalid data when updating category');
        }

        if (false === $category->update()) {
            throw new CannotEditCategoryException(
                sprintf('Failed to edit Category with id "%s".', $category->id)
            );
        }
    }
}
