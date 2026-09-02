<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
