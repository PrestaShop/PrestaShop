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

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\AddProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles @see AddProductCommand using legacy object model
 */
final class AddProductHandler implements AddProductHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        Tools $tools
    ) {
        $this->productRepository = $productRepository;
        $this->tools = $tools;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductCommand $command): ProductId
    {
        $localizedNames = $command->getLocalizedNames();
        $localizedLinkRewrites = [];

        foreach ($localizedNames as $langId => $name) {
            if (empty($name)) {
                continue;
            }

            $localizedLinkRewrites[$langId] = $this->tools->linkRewrite($name);
        }

        $product = $this->productRepository->create(
            $command->getLocalizedNames(),
            $localizedLinkRewrites,
            $command->getProductType()->getValue(),
            $command->getShopId()
        );

        return new ProductId((int) $product->id);
    }
}
