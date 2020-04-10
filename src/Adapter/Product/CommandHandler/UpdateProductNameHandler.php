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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductNameCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductNameHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShopException;

final class UpdateProductNameHandler extends AbstractProductHandler implements UpdateProductNameHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(UpdateProductNameCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());

        if (null !== $command->getLocalizedNames()) {
            $product->name = $command->getLocalizedNames();
        }

        try {
            if (!$product->save()) {
                throw new ProductException(sprintf('Failed to update product #%s name', $product->id));
            }
        } catch (PrestaShopException $e) {
            throw new ProductException(
                sprintf('Something went wrong when trying to update product #%s name', $product->id),
                0,
                $e
            );
        }
    }
}
