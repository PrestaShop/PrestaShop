<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationCarriersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

final class CombinationCarriersCommandsBuilder implements CombinationCommandsBuilderInterface
{
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (!isset($formData['carriers'])) {
            return [];
        }

        return [new SetCombinationCarriersCommand($combinationId->getValue(), $formData['carriers'], $singleShopConstraint)];
    }
}
