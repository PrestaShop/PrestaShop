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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddBasicProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\AddBasicProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShopException;
use Product;

final class AddBasicProductHandler implements AddBasicProductHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(AddBasicProductCommand $command): ProductId
    {
        $product = new Product();
        $product->name = $command->getLocalizedNames();
        //@todo: check is there anything more than this for product type
        $product->is_virtual = $command->getType() === ProductType::TYPE_VIRTUAL;
        $product->price = $command->getPrice();
        $product->quantity = $command->getQuantity();

        try {
            if (!$product->add()) {
                throw new ProductException('Failed to add new basic product');
            }
        } catch (PrestaShopException $e) {
            throw new ProductException('Error occurred when trying to add new basic product.', 0, $e);
        }

        $product->addToCategories($command->getCategoryIds());

        return new ProductId((int) $product->id);
    }
}
