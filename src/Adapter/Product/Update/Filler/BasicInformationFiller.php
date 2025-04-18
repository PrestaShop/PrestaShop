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

use PrestaShop\PrestaShop\Adapter\Domain\LocalizedObjectModelTrait;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use Product;

/**
 * Fills product properties which can be considered as a basic product information
 */
class BasicInformationFiller implements ProductFillerInterface
{
    use LocalizedObjectModelTrait;

    /**
     * @var int
     */
    private $defaultLanguageId;

    /**
     * @param int $defaultLanguageId
     */
    public function __construct(
        int $defaultLanguageId
    ) {
        $this->defaultLanguageId = $defaultLanguageId;
    }

    /**
     * {@inheritDoc}
     */
    public function fillUpdatableProperties(Product $product, UpdateProductCommand $command): array
    {
        $updatableProperties = [];

        $localizedNames = $command->getLocalizedNames();
        if (null !== $localizedNames) {
            $defaultName = $localizedNames[$this->defaultLanguageId] ?? $product->name[$this->defaultLanguageId];
            // Go through all the product languages and make sure name is filled for each of them
            if (!empty($defaultName)) {
                $productLanguages = array_keys($product->name);
                foreach ($productLanguages as $languageId) {
                    // Prevent forcing an empty value and use the default language instead
                    if (isset($localizedNames[$languageId]) && empty($localizedNames[$languageId])) {
                        $localizedNames[$languageId] = $defaultName;
                    } elseif (empty($product->name[$languageId]) && empty($localizedNames[$languageId])) {
                        // If no update value is specified but current value is empty use the default language as fallback
                        $localizedNames[$languageId] = $defaultName;
                    }
                }
            }

            $this->fillLocalizedValues($product, 'name', $localizedNames, $updatableProperties);
        }

        $localizedDescriptions = $command->getLocalizedDescriptions();
        if (null !== $localizedDescriptions) {
            $this->fillLocalizedValues($product, 'description', $localizedDescriptions, $updatableProperties);
        }

        $localizedShortDescriptions = $command->getLocalizedShortDescriptions();
        if (null !== $localizedShortDescriptions) {
            $this->fillLocalizedValues($product, 'description_short', $localizedShortDescriptions, $updatableProperties);
        }

        return $updatableProperties;
    }
}
