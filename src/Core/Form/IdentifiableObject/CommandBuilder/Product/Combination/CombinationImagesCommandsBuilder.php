<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\RemoveAllCombinationImagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationImagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Builds commands from command images form type
 */
class CombinationImagesCommandsBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (!isset($formData['images'])) {
            return [];
        }

        if (empty($formData['images'])) {
            return [new RemoveAllCombinationImagesCommand($combinationId->getValue())];
        }

        $imageIds = array_map('intval', $formData['images']);
        $command = new SetCombinationImagesCommand($combinationId->getValue(), $imageIds);

        return [$command];
    }
}
