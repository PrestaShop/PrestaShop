<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AssignProductToCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\AssignProductToCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotAssignProductToCategoryException;

/**
 * Adds a category to a product
 *
 * @internal
 */
final class AssignProductToCategoryHandler extends AbstractObjectModelHandler implements AssignProductToCategoryHandlerInterface
{
    /**
     * @param AssignProductToCategoryCommand $command
     */
    public function handle(AssignProductToCategoryCommand $command)
    {
        $this->assignProductToCategory($command);
    }

    /**
     * @param AssignProductToCategoryCommand $command
     *
     * @throws CannotAssignProductToCategoryException
     */
    private function assignProductToCategory(AssignProductToCategoryCommand $command)
    {
        $productDataProvider = new ProductDataProvider();
        $product = $productDataProvider->getProductInstance($command->getProductId()->getValue());
        $product->addToCategories($command->getCategoryId()->getValue());
        if (false === $product->save()) {
            throw new CannotAssignProductToCategoryException(sprintf('Failed to add category to product %d', $command->getProductId()->getValue()));
        }
    }
}
