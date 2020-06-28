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

use PrestaShopException;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductStockCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductStockCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductStockException;
use StockAvailable;

/**
 * @internal
 */
class UpdateProductStockCommandHandler extends AbstractProductHandler implements UpdateProductStockCommandHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductStockCommand $command): void
    {
        $product = $this->getFullProduct($command->getProductId());
        $stockAvailable = $this->getStockAvailable($command);

        if (null !== $command->useAdvancedStockManagement()) {
            $this->checkAdvancedStockIsAuthorized($command);
            $product->setAdvancedStockManagement($command->useAdvancedStockManagement());
            if (!$command->useAdvancedStockManagement() && null !== $stockAvailable && (bool) $stockAvailable->depends_on_stock) {
                StockAvailable::setProductDependsOnStock($product->id, 0);
            }
        }
    }

    /**
     * @param UpdateProductStockCommand $command
     *
     * @return StockAvailable|null
     *
     * @throws ProductStockException
     */
    private function getStockAvailable(UpdateProductStockCommand $command): ?StockAvailable
    {
        // @todo manage combination later (unless it is done in another handler)
        $stockAvailableId = StockAvailable::getStockAvailableIdByProductId($command->getProductId()->getValue());
        // Stock might not be set for this product yet
        if ($stockAvailableId <= 0) {
            return null;
        }

        try {
            $stockAvailable = new StockAvailable($stockAvailableId);

            if ((int) $stockAvailable->id !== $stockAvailableId) {
                throw new ProductStockException(
                    sprintf(
                        'StockAvailable #%s was not found',
                        $stockAvailableId
                    ),
                    ProductStockException::NOT_FOUND
                );
            }
        } catch (PrestaShopException $e) {
            throw new ProductStockException(
                sprintf('Error occurred when trying to get stock available #%s', $stockAvailableId),
                ProductStockException::NOT_FOUND,
                $e
            );
        }

        return $stockAvailable;
    }

    private function checkAdvancedStockIsAuthorized(UpdateProductStockCommand $command): void
    {
        if (!$command->useAdvancedStockManagement()) {
            return;
        }
        if (!(bool) $this->configuration->get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            throw new ProductStockException(
                'You cannot perform this action when PS_ADVANCED_STOCK_MANAGEMENT is disabled',
                ProductStockException::ADVANCED_STOCK_MANAGEMENT_DISABLED
            );
        }
    }
}
