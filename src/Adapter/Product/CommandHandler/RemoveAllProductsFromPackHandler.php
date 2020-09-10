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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use Pack;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllProductsFromPackCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\RemoveAllProductsFromPackHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductPackException;
use PrestaShopException;

/**
 * Handles @see RemoveAllProductsFromPackCommand using legacy object model
 */
final class RemoveAllProductsFromPackHandler extends AbstractProductHandler implements RemoveAllProductsFromPackHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(RemoveAllProductsFromPackCommand $command): void
    {
        $packId = $command->getPackId();
        $pack = $this->getProduct($packId);

        try {
            if (false === Pack::deleteItems($pack->id)) {
                throw new ProductPackException(
                    sprintf('Failed removing all products from pack #%d', $pack->id),
                    ProductPackException::FAILED_DELETING_PRODUCTS_FROM_PACK
                );
            }
        } catch (PrestaShopException $e) {
            throw new ProductPackException(
                sprintf('Error occurred when trying to remove products from pack #%s', $pack->id),
                0,
                $e
            );
        } finally {
            Pack::resetStaticCache();
        }

        //reset cache_default_attribute
        $pack->setDefaultAttribute(CombinationId::NO_COMBINATION);
    }
}
