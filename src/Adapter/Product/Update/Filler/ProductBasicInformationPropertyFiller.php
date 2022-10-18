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

namespace PrestaShop\PrestaShop\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use Product;

/**
 * Fills product properties which can be considered as a basic product information
 */
class ProductBasicInformationPropertyFiller implements ProductUpdatablePropertyFillerInterface
{
    /**
     * @var int
     */
    private $defaultLanguageId;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @param int $defaultLanguageId
     * @param Tools $tools
     */
    public function __construct(
        int $defaultLanguageId,
        Tools $tools
    ) {
        $this->defaultLanguageId = $defaultLanguageId;
        $this->tools = $tools;
    }

    /**
     * {@inheritDoc}
     */
    public function fillUpdatableProperties(Product $product, UpdateProductCommand $command): array
    {
        $updatableProperties = [];

        $localizedNames = $command->getLocalizedNames();
        if (null !== $localizedNames) {
            $defaultName = $localizedNames[$this->defaultLanguageId];
            // Go through all the product languages and make sure name is filled for each of them
            $productLanguages = array_keys($product->name);
            foreach ($productLanguages as $languageId) {
                if (empty($localizedNames[$languageId])) {
                    $localizedNames[$languageId] = $defaultName;
                }
            }
            $product->name = $localizedNames;
            $updatableProperties['name'] = array_keys($localizedNames);
        }

        foreach ($product->link_rewrite as $langId => $linkRewrite) {
            if (!empty($linkRewrite) || empty($product->name[$langId])) {
                continue;
            }

            $product->link_rewrite[$langId] = $this->tools->linkRewrite($product->name[$langId]);
            $updatableProperties['link_rewrite'][] = $langId;
        }

        $localizedDescriptions = $command->getLocalizedDescriptions();
        if (null !== $localizedDescriptions) {
            $product->description = $localizedDescriptions;
            $updatableProperties['description'] = array_keys($localizedDescriptions);
        }

        $localizedShortDescriptions = $command->getLocalizedShortDescriptions();
        if (null !== $localizedShortDescriptions) {
            $product->description_short = $localizedShortDescriptions;
            $updatableProperties['description_short'] = array_keys($localizedShortDescriptions);
        }

        return $updatableProperties;
    }
}
