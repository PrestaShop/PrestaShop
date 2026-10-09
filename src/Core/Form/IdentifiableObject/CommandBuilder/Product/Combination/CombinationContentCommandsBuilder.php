<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\Command\UpdateCombinationContentCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

final class CombinationContentCommandsBuilder implements CombinationCommandsBuilderInterface
{
    public function __construct(
        private readonly string $modifyAllNamePrefix,
    ) {
    }

    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $config = new CommandBuilderConfig($this->modifyAllNamePrefix);
        $config
            ->addMultiShopField('[content][description]', 'setLocalizedDescriptions', DataField::TYPE_ARRAY)
            ->addMultiShopField('[content][description_short]', 'setLocalizedShortDescriptions', DataField::TYPE_ARRAY)
            ->addMultiShopField('[content][link_rewrite]', 'setLocalizedLinkRewrites', DataField::TYPE_ARRAY)
            ->addMultiShopField('[content][meta_description]', 'setLocalizedMetaDescriptions', DataField::TYPE_ARRAY)
            ->addMultiShopField('[content][meta_title]', 'setLocalizedMetaTitles', DataField::TYPE_ARRAY)
        ;

        return (new CommandBuilder($config))->buildCommands(
            $formData,
            new UpdateCombinationContentCommand($combinationId->getValue(), $singleShopConstraint),
            new UpdateCombinationContentCommand($combinationId->getValue(), ShopConstraint::allShops())
        );
    }
}
