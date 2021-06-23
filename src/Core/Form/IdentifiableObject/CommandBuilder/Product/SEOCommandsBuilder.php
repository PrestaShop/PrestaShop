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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductSeoCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Builder used to build UpdateSEO
 */
class SEOCommandsBuilder implements ProductCommandsBuilderInterface
{
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (!isset($formData['seo'])) {
            return [];
        }

        $seoData = $formData['seo'] ?? [];
        $redirectionData = $formData['seo']['redirect_option'] ?? [];
        $command = new UpdateProductSeoCommand($productId->getValue());

        if (isset($seoData['meta_title'])) {
            $command->setLocalizedMetaTitles($seoData['meta_title']);
        }
        if (isset($seoData['meta_description'])) {
            $command->setLocalizedMetaDescriptions($seoData['meta_description']);
        }
        if (isset($seoData['link_rewrite'])) {
            $command->setLocalizedLinkRewrites($seoData['link_rewrite']);
        }

        if (isset($redirectionData['type'])) {
            $targetId = (int) ($redirectionData['target'] ?? 0);
            $command->setRedirectOption($redirectionData['type'], $targetId);
        }

        return [$command];
    }
}
