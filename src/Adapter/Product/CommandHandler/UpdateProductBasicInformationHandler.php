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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductBasicInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductBasicInformationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShopException;
use Product;

/**
 * Handles command for product basic information update using legacy object model
 */
final class UpdateProductBasicInformationHandler extends AbstractProductHandler implements UpdateProductBasicInformationHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * Null values are not updated, because are considered unchanged
     */
    public function handle(UpdateProductBasicInformationCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());

        if (null !== $command->getLocalizedNames()) {
            $product->name = $command->getLocalizedNames();
            $this->validateLocalizedNames($product);
        }

        if (null !== $command->isVirtual()) {
            $product->is_virtual = $command->isVirtual();
        }

        try {
            if (false === $product->update()) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to update product #%s basic information', $product->id),
                    CannotUpdateProductException::FAILED_UPDATE_BASIC_INFO
                );
            }
        } catch (PrestaShopException $e) {
            throw new CannotUpdateProductException(
                sprintf('Error occurred when trying to update product #%s basic information', $product->id),
                CannotUpdateProductException::FAILED_UPDATE_BASIC_INFO,
                $e
            );
        }
    }
}
