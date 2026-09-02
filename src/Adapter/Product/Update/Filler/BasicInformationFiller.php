<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
