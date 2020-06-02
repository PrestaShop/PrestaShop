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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductBasicInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductBasicInformationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use Product;

/**
 * Handles command for product basic information update using legacy object model
 */
final class UpdateProductBasicInformationHandler implements UpdateProductBasicInformationHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * Null values are not updated, because it is considered as unchanged
     */
    public function handle(UpdateProductBasicInformationCommand $command): void
    {
        //@todo: get product from abstractHandler in another PR
        $productId = $command->getProductId();
        $product = new Product($productId->getValue());

        if (null !== $command->getLocalizedNames()) {
            $product->name = $command->getLocalizedNames();
            $this->validateLocalizedNames($product);
        }

        if (null !== $command->isVirtual()) {
            $product->is_virtual = $command->isVirtual();
        }

        //@todo: wrap in try catch
        $product->update();
    }

    /**
     * @todo: move to abstract? AddProductCommand uses the same
     *
     * @param Product $product
     *
     * @throws ProductConstraintException
     * @throws \PrestaShopException
     */
    private function validateLocalizedNames(Product $product): void
    {
        foreach ($product->name as $langId => $name) {
            if (true !== $product->validateField('name', $name, $langId)) {
                throw new ProductConstraintException(
                    sprintf(
                        'Invalid localized product name for language with id "%s"',
                        $langId
                    ),
                    ProductConstraintException::INVALID_NAME
                );
            }
        }
    }
}
