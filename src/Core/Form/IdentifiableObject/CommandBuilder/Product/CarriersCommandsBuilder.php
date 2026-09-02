<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetCarriersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

final class CarriersCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * @var string
     */
    private $modifyAllNamePrefix;

    /**
     * @param string $modifyAllNamePrefix
     */
    public function __construct(
        string $modifyAllNamePrefix
    ) {
        $this->modifyAllNamePrefix = $modifyAllNamePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (!isset($formData['shipping']['carriers'])) {
            return [];
        }

        if (!empty($formData['shipping'][$this->modifyAllNamePrefix . 'carriers'])) {
            $shopConstraint = ShopConstraint::allShops();
        } else {
            $shopConstraint = $singleShopConstraint;
        }

        return [
            new SetCarriersCommand(
                $productId->getValue(),
                $formData['shipping']['carriers'],
                $shopConstraint
            ),
        ];
    }
}
