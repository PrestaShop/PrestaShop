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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\DeleteProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShopException;
use Product;

/**
 * Deletes product.
 *
 * @internal
 */
final class DeleteProductHandler implements DeleteProductHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteProductException
     * @throws ProductNotFoundException
     */
    public function handle(DeleteProductCommand $command)
    {
        $product = new Product($command->getProductId()->getValue());

        if (0 >= $product->id) {
            throw new ProductNotFoundException(
                sprintf('Product not found with given id %s', $command->getProductId()->getValue())
            );
        }

        try {
            if (false === $product->delete()) {
                throw new CannotDeleteProductException(
                    sprintf(
                        'Cannot delete product widht id %s',
                        $command->getProductId()->getValue()
                    )
                );
            }
        } catch (PrestaShopException $exception) {
            throw new CannotDeleteProductException(
                'An unexpected error occurred when deleting product with id %s',
                0,
                $exception
            );
        }
    }
}
