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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class TagsCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $commands = [];
        $seoData = $formData['seo'] ?? [];

        if (isset($seoData['tags'])) {
            if (!empty($seoData['tags'])) {
                if (!is_array($seoData['tags'])) {
                    throw new InvalidArgumentException('Expected tags to be a localized array');
                }

                $parsedTags = [];
                $allEmpty = true;
                foreach ($seoData['tags'] as $langId => $rawTags) {
                    $parsedTags[$langId] = !empty($rawTags) ? explode(',', $rawTags) : [];
                    $allEmpty = $allEmpty && empty($rawTags);
                }

                if ($allEmpty) {
                    $commands[] = new RemoveAllProductTagsCommand($productId->getValue());
                } else {
                    $commands[] = new SetProductTagsCommand(
                        $productId->getValue(),
                        $parsedTags
                    );
                }
            } else {
                $commands[] = new RemoveAllProductTagsCommand($productId->getValue());
            }
        }

        return $commands;
    }
}
