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

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductSeoCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductSeoHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use Product;

/**
 * Handles @var UpdateProductSeoCommand using legacy object model
 */
class UpdateProductSeoHandler extends AbstractProductHandler implements UpdateProductSeoHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductSeoCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());
        $this->fillUpdatableFieldsWithCommandData($product, $command);

        $this->performUpdate($product, CannotUpdateProductException::FAILED_UPDATE_SEO);
    }

    /**
     * @param Product $product
     * @param UpdateProductSeoCommand $command
     */
    private function fillUpdatableFieldsWithCommandData(Product $product, UpdateProductSeoCommand $command): void
    {
        $redirectOption = $command->getRedirectOption();

        if (null !== $redirectOption) {
            $product->redirect_type = $redirectOption->getRedirectType();
            $product->id_type_redirected = $redirectOption->getRedirectTargetId();
            $this->fieldsToUpdate['redirect_type'] = true;
            $this->fieldsToUpdate['id_type_redirected'] = true;
        }

        if (null !== $command->getLocalizedMetaDescriptions()) {
            $product->meta_description = $command->getLocalizedMetaDescriptions();
            $this->fieldsToUpdate['meta_description'] = true;
        }

        if (null !== $command->getLocalizedMetaTitles()) {
            $product->meta_title = $command->getLocalizedMetaTitles();
            $this->fieldsToUpdate['meta_title'] = true;
        }

        if (null !== $command->getLocalizedLinkRewrites()) {
            $product->link_rewrite = $command->getLocalizedLinkRewrites();
            $this->fieldsToUpdate['link_rewrite'] = true;
        }
    }
}
