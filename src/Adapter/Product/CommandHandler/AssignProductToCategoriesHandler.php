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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AssignProductToCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\AssignProductToCategoriesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;

/**
 * Handles AssignProductToCategoriesCommand using legacy object model
 */
final class AssignProductToCategoriesHandler extends AbstractProductHandler implements AssignProductToCategoriesHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AssignProductToCategoriesCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());

        $categoryIds = array_map(function (CategoryId $categoryId) {
            return $categoryId->getValue();
        }, $command->getCategoryIds());

        $product->addToCategories($categoryIds);
        $product->id_category_default = $command->getDefaultCategoryId()->getValue();

        $this->performUpdate($product, CannotUpdateProductException::FAILED_ASSIGN_TO_CATEGORIES);
    }
}
