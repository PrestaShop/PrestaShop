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
use PrestaShop\PrestaShop\Core\Domain\Category\Command\UpdateCategoryPositionCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\UpdateCategoryPositionHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;

/**
 * Updates category position using legacy object model
 */
final class UpdateCategoryPositionHandler implements UpdateCategoryPositionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCategoryPositionCommand $command)
    {
        $parentCategoryId = $command->getParentCategoryId()->getValue();
        $categoryId = $command->getCategoryId()->getValue();

        $position = null;

        foreach ($command->getPositions() as $key => $value) {
            list(, $positionParentCategoryId, $positionCategoryId) = explode('_', $value);

            if ((int) $positionParentCategoryId === $parentCategoryId && (int) $positionCategoryId === $categoryId) {
                $position = $key;

                break;
            }
        }

        if (null === $position) {
            throw new CategoryException('Category position cannot be updated');
        }

        $category = new Category($categoryId);

        if (!$category->id) {
            throw new CategoryNotFoundException($command->getCategoryId(), sprintf('Category with id "%s" was not found', $categoryId));
        }

        if ($category->updatePosition($command->getWay(), $position)) {
            /* Position '0' was not found in given positions so try to reorder parent category*/
            if (!$command->isFoundFirst()) {
                Category::cleanPositions((int) $category->id_parent);
            }
        }
    }
}
