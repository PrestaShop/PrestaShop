<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class CombinationCommandsBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * @var iterable<CombinationCommandsBuilderInterface>
     */
    private $commandBuilders;

    /**
     * @param iterable<CombinationCommandsBuilderInterface> $commandBuilders
     */
    public function __construct(iterable $commandBuilders)
    {
        $this->commandBuilders = $commandBuilders;
    }

    /**
     * {@inheritDoc}
     */
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $commandCollection = [];
        foreach ($this->commandBuilders as $commandBuilder) {
            $commands = $commandBuilder->buildCommands($combinationId, $formData, $singleShopConstraint);

            if (!empty($commands)) {
                $commandCollection = array_merge($commandCollection, $commands);
            }
        }

        return $commandCollection;
    }
}
