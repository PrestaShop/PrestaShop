<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

/**
 * Builds commands from command stock form type only fields related to StockAvailable
 */
class UpdateCombinationStockAvailableCommandsBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * @var string
     */
    private $modifyAllNamePrefix;

    /**
     * @param string $modifyAllNamePrefix
     */
    public function __construct(string $modifyAllNamePrefix)
    {
        $this->modifyAllNamePrefix = $modifyAllNamePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $config = new CommandBuilderConfig($this->modifyAllNamePrefix);
        $config
            ->addMultiShopField('[stock][quantities][delta_quantity][delta]', 'setDeltaQuantity', DataField::TYPE_INT)
            ->addMultiShopField('[stock][quantities][fixed_quantity]', 'setFixedQuantity', DataField::TYPE_INT)
            ->addMultiShopField('[stock][options][stock_location]', 'setLocation', DataField::TYPE_STRING)
        ;

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateCombinationStockAvailableCommand($combinationId->getValue(), $singleShopConstraint);
        $allShopsCommand = new UpdateCombinationStockAvailableCommand($combinationId->getValue(), ShopConstraint::allShops());

        return $commandBuilder->buildCommands($formData, $shopCommand, $allShopsCommand);
    }
}
